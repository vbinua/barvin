window.addEventListener('DOMContentLoaded', () => {
    createColoring();
    dGuest();
    addRequest();
    initStripe();
    showGetPayBox();
    bindGitemClicks();
});

const filesStore = [];
let prepared = false;
const addedFieldIds = new Set();
let currentPaymentAmount = 0;
let isPaidSession = false;
let freeFilesProcessed = 0;

function createColoring() {
    const previewAreaEl = document.querySelector('.uploader__preview_area');
    const uploaderButton = document.querySelector('.uploader__button');
    const quizFooterEdit = document.querySelector('.quiz__m-footer-edit');
    const dropArea = document.querySelector('.uploader__drop_area');

    const uploader = new plupload.Uploader({
        browse_button: previewAreaEl, drop_element: previewAreaEl, multi_selection: true, filters: {
            mime_types: [{title: 'Images', extensions: 'jpg,jpeg,png,webp'}], max_file_size: '50mb'
        }
    });

    uploader.init();

    previewAreaEl.addEventListener('dragenter', () => {
        previewAreaEl.classList.add('dragover');
    });

    previewAreaEl.addEventListener('dragleave', () => {
        previewAreaEl.classList.remove('dragover');
    });

    previewAreaEl.addEventListener('drop', () => {
        previewAreaEl.classList.remove('dragover');
    });

    uploader.bind('FilesAdded', function (up, files) {
        uploaderButton.classList.add('uploader__button_active');
        quizFooterEdit.classList.add('active');

        files.forEach((file, index) => {
            const sourceFile = file.getNative?.() || file.getSource?.().getAsBlob?.();

            if (!sourceFile) return;

            sendFile(sourceFile, 'add');

            const reader = new FileReader();

            reader.onload = function (e) {
                const preview = document.createElement('div');
                preview.className = 'preview__item';

                const newId = `file-${index}-${Date.now()}`;

                preview.setAttribute('data-attach-id', newId);

                const img = document.createElement('img');
                img.src = e.target.result;

                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'delete__btn';
                deleteBtn.textContent = '✕';

                showCreateButton();

                deleteBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    showCreateButton();

                    sendFile(sourceFile, 'remove');
                    preview.remove();
                });

                preview.appendChild(img);
                preview.appendChild(deleteBtn);

                dropArea.before(preview);
            };

            reader.readAsDataURL(sourceFile);
        });

        up.refresh();
    });

    function sendFile(file, action = 'add') {
        if (!file) return;

        if (action === 'add') {
            filesStore.push(file);
        }

        if (action === 'remove') {
            const index = filesStore.indexOf(file);
            if (index !== -1) {
                filesStore.splice(index, 1);
            }
        }
    }
}

function addRequest() {
    const button = document.querySelector('.quiz__start-creating');
    if (!button) return;

    button.addEventListener('click', (e) => {
        e.preventDefault();

        const guest_id = getGuestId();
        const resultBox = document.querySelector('.uploader__generate .result__items');
        const items = document.querySelectorAll('.uploader__preview_area .preview__item');
        const uploadCount = document.querySelectorAll('.body__uploader .uploader__preview_area .preview__item').length;
        const quizSlug = document.querySelector('.quiz-edit')?.getAttribute('data-quiz-slug') ?? '';
        const postId = getUrlParam('edit-book');
        const notificationBox = document.querySelector('.notification-error');

        if (!uploadCount) {
            notificationBox.style.display = 'flex';
            notificationBox.innerHTML = 'You didn’t add the photo, please add it';
            setTimeout(() => {
                notificationBox.style.display = 'none';
            }, 4000);
            return;
        }

        if (!items.length || !resultBox || !postId) return;

        sendTrialRequest(guest_id)
            .then(response => {
                const remaining = response?.data?.remaining ?? 0;

                if (remaining <= 0 && !isPaidSession) {
                    statusDefaultBlock('hide');
                    statusResultBlock('show');
                    statusLoaderBlock('hide');
                    loaderToButton(button, 'hide');

                    resultBox.innerHTML = '';
                    document.querySelector('.result__message').style.display = 'none';

                    addPrepareResultItems(items.length);

                    showGetButton();

                    showPayForImages(true, 'create');
                    return;
                }

                let createCount = 0;
                prepared = false;
                addedFieldIds.clear();

                statusDefaultBlock('hide');
                statusResultBlock('hide');
                statusLoaderBlock('show');
                loaderToButton(button, 'show');

                resultBox.innerHTML = '';
                document.querySelector('.result__message').style.display = 'none';

                createCount = items.length > remaining && !isPaidSession ? items.length - remaining : 0;

                const formData = new FormData();
                let filesToSend = [];
                let isAppend = 0;

                if (!isPaidSession) {
                    filesToSend = filesStore.slice(0, remaining);
                    freeFilesProcessed = filesToSend.length;
                } else {
                    filesToSend = filesStore.slice(freeFilesProcessed);
                    isAppend = 1;
                }

                filesToSend.forEach(file => {
                    formData.append('files[]', file);
                });

                formData.append('action', 'create_colorings');
                formData.append('nonce', ajaxData.nonce);
                formData.append('guest_id', guest_id);
                formData.append('post_id', postId);
                formData.append('quizSlug', quizSlug);
                formData.append('count', filesToSend.length);
                formData.append('is_append', isAppend);

                formData.append('count', isPaidSession ? 999 : remaining);

                waitForResults(postId, '.result__items', '.events__loader', 300, createCount);

                return jQuery.ajax({
                    url: ajaxData.ajaxUrl, type: 'POST', contentType: false, processData: false, data: formData
                });

            })
            .catch(err => {
                loaderToButton(button, 'hide');
                console.error(err);
            });
    });
}

function dGuest() {
    const guest_id = getGuestId();

    sendTrialRequest(guest_id)
        .then(response => {
            const label = document.querySelector('.uploader__content_label');
            const labelCount = document.querySelector('.uploader__content_label span');
            const generationBlock = document.querySelector('.quiz__information__about_generation');
            const generationBlockCount = document.querySelector('.quiz__information__about_generation span span');

            if (response.data.remaining) {
                if (labelCount) {
                    labelCount.innerHTML = response.data.remaining;
                }

                if (generationBlockCount) {
                    generationBlockCount.innerHTML = response.data.remaining;
                }

                if (label) {
                    setTimeout(() => {
                        label.classList.add('active');
                    }, 1500);
                }
            } else {
                label.style.display = 'none';
                generationBlock.style.display = 'none';
            }
        })
        .catch(err => {
            console.error(err);
        });
}


// Handlers
function sendTrialRequest(guest_id) {
    return new Promise((resolve, reject) => {
        (function ($) {
            $.ajax({
                url: ajaxData.ajaxUrl, type: 'POST', data: {
                    action: 'coloring_can_use_trial_ajax', guest_id: guest_id, _ajax_nonce: ajaxData.nonce
                }, success(response) {
                    resolve(response);
                }, error(err) {
                    reject(err);
                }
            });
        })(jQuery);
    });
}

function waitForResults(postId, resultBoxSelector, loaderSelector, maxAttempts = 300, createCount = 0) {
    const resultBox = document.querySelector(resultBoxSelector);
    if (!resultBox) return;

    const guestId = getGuestId();
    let attempts = 0;

    const timer = setInterval(() => {
        attempts++;

        fetch(ajaxData.ajaxUrl, {
            method: 'POST', headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            }, body: new URLSearchParams({
                action: 'check_coloring_result', post_id: postId, guest_id: guestId
            })
        })
            .then(res => res.json())
            .then(response => {
                if (!response.success || !Array.isArray(response.data)) return;

                let addedThisTick = 0;

                response.data.forEach(item => {
                    if (!item.field_id || !Array.isArray(item.urls)) return;
                    if (addedFieldIds.has(item.field_id)) return;

                    addedFieldIds.add(item.field_id);
                    addedThisTick++;

                    const el = document.createElement('div');
                    el.className = 'result__item';

                    el.innerHTML = `
                        <div class="item__title">${item.field_id}</div>
                
                        ${item.urls.map((url, index) => ` 
                             <div class="item__photo">
                                  <img src="${url}" data-index="${index}">
                             </div>  
                            <div class="item__size" data-index="${index}">—</div>
                        `).join('')}
                
                        <a href="#" class="item__preview">Show</a>
                    `;

                    resultBox.prepend(el);

                    item.urls.forEach((url, index) => {
                        const img = new Image();
                        img.src = url;
                        img.onload = () => {
                            const sizeEl = el.querySelector(`.item__size[data-index="${index}"]`);

                            if (sizeEl) {
                                sizeEl.textContent = `${img.naturalWidth}×${img.naturalHeight}`;
                            }
                        };
                    });
                });

                if (createCount > 0 && !prepared) {
                    prepared = true;
                    addPrepareResultItems(createCount);
                }

                if (addedThisTick > 0) {
                    showFullImage();
                }

                const uploadCount = document.querySelectorAll('.body__uploader .uploader__preview_area .preview__item').length;
                const resultCount = document.querySelectorAll('.result__items .result__item').length;

                if (uploadCount === resultCount) {
                    clearInterval(timer);
                    statusLoaderBlock('hide');
                    statusResultBlock('show');

                    showGetButton();

                    if (createCount > 0) {
                        showPayForImages(true, 'get');
                    } else {
                        showPayForImages(true, 'free_download');
                    }

                    isPaidSession = false;
                }
            })
            .catch(() => {
                if (attempts >= maxAttempts) clearInterval(timer);
            });

        if (attempts >= maxAttempts) {
            clearInterval(timer);
            statusLoaderBlock('hide');
        }
    }, 3000);
}


// Additional
function getUrlParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

function statusDefaultBlock(status) {
    const box = document.querySelector('.uploader__generate .result__information_box');

    if (status === 'show') {
        box.classList.remove('hide')
    } else {
        box.classList.add('hide')
    }
}

function statusLoaderBlock(status) {
    const loader = document.querySelector('.result__loader');

    if (status === 'show') {
        loader.classList.remove('hide')
    } else {
        loader.classList.add('hide')
    }
}

function statusResultBlock(status) {
    const box = document.querySelector('.uploader__generate .result__items');

    if (status === 'show') {
        box.classList.remove('hide')
    } else {
        box.classList.add('hide')
    }
}

function loaderToButton(button, status) {
    if (status === 'show') {
        if (button) {
            const img = document.createElement('img');
            img.src = `${ajaxData.themeUrl}/img/loader-second.gif`;
            img.alt = 'icon';
            img.className = 'quiz__start-icon';

            button.style.pointerEvents = 'none';
            button.innerHTML = '';
            button.appendChild(img);
        }
    } else {
        button.style.pointerEvents = 'auto';
        button.innerHTML = 'Start creating';
    }
}

function showGetButton() {
    const startCreating = document.querySelector('.quiz__start-creating');
    const getColorings = document.querySelector('.quiz__get-colorings');
    const resultMessage = document.querySelector('.result__message');

    if (startCreating && getColorings && resultMessage) {
        startCreating.style.display = 'none';
        getColorings.style.display = 'block';
        resultMessage.style.display = 'block';
        resultMessage.innerHTML = 'All the coloring are ready, get them right now';
    }

    showGetPayBox();
}

function showCreateButton() {
    const startCreating = document.querySelector('.quiz__start-creating');
    const getColorings = document.querySelector('.quiz__get-colorings');

    if (startCreating && getColorings) {
        startCreating.style.display = 'flex';
        getColorings.style.display = 'none';
        startCreating.style.pointerEvents = 'auto';
        startCreating.innerHTML = 'Start creating';
    }
}

function getGuestId() {
    if (!document.body.classList.contains('logged-in')) {
        if (!localStorage.getItem('guest_id')) {
            localStorage.setItem('guest_id', crypto.randomUUID());
        }
    }

    return localStorage.getItem('guest_id');
}

function addPrepareResultItems(count) {
    const parent = document.querySelector('.result__items');
    count = Number(count);

    if (!parent || count <= 0) return;

    const images = [
        'coloring_defgrhtetee.jpg',
        'coloring_dfewgeww.jpg',
        'coloring_few231gweDfwrcde.jpeg',
        'coloring_fsrgehtett.jpg',
        'coloring_fwefgbs.jpg',
        'coloring_greeadefrew.jpg',
        'coloring_htrhtwaddwq.jpg',
        'coloring_qwdagresvgr.jpg',
        'coloring_tdwqfgwrq.jpg',
        'coloring_tredewghtdwd.jpg'
    ];

    let storedNames = JSON.parse(localStorage.getItem('result_file_names')) || [];

    for (let i = 0; i < count; i++) {
        const item = document.createElement('div');
        item.className = 'result__item result__gitem';

        if (!storedNames[i]) {
            storedNames[i] = generateRandomFileName();
        }

        const fileName = storedNames[i];

        const imgIndex = i % images.length;
        const imgSrc = `${ajaxData.themeUrl}/img/colorings/${images[imgIndex]}`;

        item.innerHTML = `
            <div class="item__photo">
                <img src="${imgSrc}" alt="coloring">
            </div>
            <div class="item__title">coloring_${fileName}</div>
            <div class="item__size">—</div>
            <a href="#" class="item__preview">Show</a>
        `;

        parent.appendChild(item);

        const img = item.querySelector('img');
        const sizeEl = item.querySelector('.item__size');

        img.onload = () => {
            sizeEl.textContent = `${img.naturalWidth}×${img.naturalHeight}`;
        };
    }

    localStorage.setItem('result_file_names', JSON.stringify(storedNames));

    function generateRandomFileName() {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let name = '';

        for (let i = 0; i < 11; i++) {
            name += chars[Math.floor(Math.random() * chars.length)];
        }

        return `${name}.jpg`;
    }
}

function showFullImage() {
    const modal = document.getElementById('imageModal');
    if (!modal) return;

    const modalImg = modal.querySelector('img');
    if (!modalImg) return;

    const closeBtn = modal.querySelector('.closeBtn');
    if (!closeBtn) return;

    document.querySelectorAll('.result__items .item__photo').forEach(wrapper => {
        const item = wrapper.closest('.result__item');
        const img = wrapper.querySelector('img');
        if (!item || !img) return;

        wrapper.addEventListener('click', () => {
            if (item.classList.contains('result__gitem')) return;

            modal.style.display = 'block';
            modalImg.src = img.src;
        });
    });

    document.querySelectorAll('.result__items .item__preview').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();

            const item = btn.closest('.result__item');
            if (!item || item.classList.contains('result__gitem')) return;

            const img = item.querySelector('.item__photo img');
            if (!img) return;

            modal.style.display = 'block';
            modalImg.src = img.src;
        });
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    modal.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
    });
}

function initStripe() {
    stripeInstance = Stripe(ajaxData.stripePublicKey);
    const elements = stripeInstance.elements();

    cardElement = elements.create('card', {
        style: {base: {fontSize: '16px', color: '#32325d', fontFamily: 'Arial, sans-serif'}}
    });

    const mountCard = () => {
        const cardContainer = document.getElementById('card-element');
        if (cardContainer) {
            cardElement.mount('#card-element');
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountCard);
    } else {
        mountCard();
    }

    cardElement.on('change', (event) => {
        const displayError = document.getElementById('card-errors');
        displayError.textContent = event.error ? event.error.message : '';
    });

    paymentForm = document.getElementById('payment-form');
    if (paymentForm) {
        paymentForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (currentPaymentAmount <= 0) {
                alert("Amount must be greater than zero.");
                return;
            }

            const submitBtn = document.getElementById('submit-pay-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            document.getElementById('card-errors').textContent = '';

            const {paymentMethod, error} = await stripeInstance.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                submitBtn.disabled = false;
                submitBtn.textContent = 'Pay Now';
            } else {
                processPayment(paymentMethod.id, currentPaymentAmount);
            }
        });
    }
}

function showPayForImages(show = false, type = 'get') {
    const modal = document.getElementById('payPopup');
    if (!modal) return;

    const box = modal.querySelector('.content__box_block');
    const title = modal.querySelector('.content__box .content__title h3');
    const description = modal.querySelector('.content__box .content__description');
    const form = document.getElementById('payment-form');
    const contentLabel = modal.querySelector('.content__label');
    const priceBox = modal.querySelector('.content__price');
    const priceEl = modal.querySelector('.content__price span');
    const btnDownload = modal.querySelector('.content__download');
    const loader = modal.querySelector('.content__loader');

    if (title) title.style.display = 'none';
    if (description) description.style.display = 'none';
    if (form) form.style.display = 'none';
    if (btnDownload) btnDownload.style.display = 'none';
    if (priceBox) priceBox.style.display = 'none';
    if (contentLabel) contentLabel.style.display = 'none';

    if (loader) loader.style.display = 'block';
    if (box) box.style.display = 'none';

    if (show) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    } else {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    }

    const guest_id = getGuestId();

    sendTrialRequest(guest_id)
        .then(response => {
            const quizSlug = document.querySelector('.quiz-edit')?.getAttribute('data-quiz-slug') ?? 'cartoon-friendly-style';
            const priceValue = Number(getPriceBySlug(quizSlug)) || 1;
            const uploadCount = document.querySelectorAll('.body__uploader .uploader__preview_area .preview__item').length;

            const resultCountP = document.querySelectorAll('.result__items .result__gitem').length;
            const resultCountFree = document.querySelectorAll('.result__items .result__item:not(.result__gitem)').length;

            let sum = resultCountP ? Math.max(0, resultCountP * priceValue) : Math.max(0, uploadCount * priceValue);
            sum = Number(sum.toFixed(2));
            currentPaymentAmount = sum;

            if (loader) loader.style.display = 'none';
            if (box) box.style.display = 'block';
            if (title) title.style.display = 'block';
            if (description) description.style.display = 'block';

            if (type === 'create' || type === 'get') {
                if (form) form.style.display = 'block';
                if (contentLabel) contentLabel.style.display = 'block';
                if (priceBox) priceBox.style.display = 'block';

                if (priceEl) priceEl.innerHTML = `$${sum.toFixed(2)}`;
                contentLabel.innerHTML = 'You get a <b>30%</b> discount';

                if (resultCountFree > 0 && resultCountP > 0) {
                    title.innerHTML = 'Unlock remaining colorings';
                    description.innerHTML = `We generated <b>${resultCountFree}</b> coloring(s) for free! Purchase to unlock the remaining <b>${resultCountP}</b> image(s).`;

                    if (btnDownload) {
                        btnDownload.style.display = 'block';
                        btnDownload.textContent = `Download ${resultCountFree} free image(s)`;

                        getUnlockedImages(getUrlParam('edit-book'), btnDownload);
                    }
                } else {
                    title.innerHTML = type === 'create' ? 'Get a high-quality coloring page right now' : 'Unlock your colorings';
                    description.innerHTML = 'You have used all your free trials. Purchase to generate your colorings!';
                }

            } else if (type === 'free_download' || type === 'download') {
                title.innerHTML = 'Your colorings are ready! 🎉';
                description.innerHTML = 'Click the button below to download your high-quality colorings without watermarks.';

                if (btnDownload) {
                    btnDownload.style.display = 'flex';
                    btnDownload.textContent = 'Download HD Images';

                    btnDownload.style.marginTop = '';
                    btnDownload.style.backgroundColor = '';
                    btnDownload.style.color = '';
                    btnDownload.style.border = '';

                    getUnlockedImages(getUrlParam('edit-book'), btnDownload);
                }
            }

            const closeBtn = modal.querySelector('.closeBtn');
            if (closeBtn) {
                closeBtn.onclick = () => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                };
            }

            modal.onclick = e => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                }
            };
        })
        .catch(console.error);
}

async function processPayment(paymentMethodId, amount) {
    const codeNumber = document.getElementById('codeNumber')?.value || '';
    const fillDate = document.getElementById('fillDate')?.value || '';
    const code = document.getElementById('code')?.value || '';

    const postId = getUrlParam('edit-book');
    const quizSlug = document.querySelector('.quiz-edit')?.getAttribute('data-quiz-slug') ?? 'cartoon-friendly-style';
    const uploadCount = document.querySelectorAll('.body__uploader .uploader__preview_area .preview__item').length;
    const resultCountP = document.querySelectorAll('.result__items .result__gitem').length;
    const itemsCount = resultCountP ? resultCountP : uploadCount;

    if (!postId) {
        alert('Error: Post ID not found.');
        return;
    }

    try {
        const data = await jQuery.ajax({
            url: ajaxData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'process_stripe_payment',
                payment_method_id: paymentMethodId,
                post_id: postId,
                quiz_slug: quizSlug,
                items_count: itemsCount,
                nonce: ajaxData.nonce,
                codeNumber: codeNumber,
                fillDate: fillDate,
                code: code
            }
        });

        if (!data.success) {
            document.getElementById('card-errors').textContent = data.data?.message || "Payment error";
            resetPayBtn();
        } else if (data.data && data.data.requires_action) {
            const {error: errorAction} = await stripeInstance.confirmCardPayment(data.data.payment_intent_client_secret);
            if (errorAction) {
                document.getElementById('card-errors').textContent = errorAction.message;
                resetPayBtn();
            } else {
                paymentSuccessfulAction(postId);
            }
        } else {
            paymentSuccessfulAction(postId);
        }
    } catch (err) {
        console.error('AJAX Error:', err);
        const errorMessage = err.responseJSON?.data?.message || "Server connection error.";
        document.getElementById('card-errors').textContent = errorMessage;
        resetPayBtn();
    }
}

function paymentSuccessfulAction() {
    resetPayBtn();

    if (paymentForm) paymentForm.reset();
    if (cardElement) cardElement.clear();

    const isGenerated = document.querySelectorAll('.result__items .result__item:not(.result__gitem)').length > 0;

    if (isGenerated) {
        showPayForImages(true, 'free_download');
    } else {
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';

        isPaidSession = true;
        document.querySelector('.quiz__start-creating').click();
    }
}

function getUnlockedImages(postId, downloadBtn) {
    const newBtn = downloadBtn.cloneNode(true);
    downloadBtn.parentNode.replaceChild(newBtn, downloadBtn);

    fetch(ajaxData.ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        body: new URLSearchParams({
            action: 'get_unlocked_colorings',
            post_id: postId,
            nonce: ajaxData.nonce
        })
    }).then(res => res.json()).then(response => {
        if (response.success && response.data.urls && response.data.urls.length > 0) {

            newBtn.addEventListener('click', async (e) => {
                e.preventDefault();

                const originalText = newBtn.textContent;

                newBtn.textContent = 'Downloading...';
                newBtn.style.pointerEvents = 'none';

                for (let i = 0; i < response.data.urls.length; i++) {
                    await downloadImage(response.data.urls[i], `coloring_${postId}_${i + 1}.jpg`);
                }

                setTimeout(() => {
                    newBtn.textContent = originalText;
                    newBtn.style.pointerEvents = 'auto';
                }, 2000);
            });
        }
    }).catch(err => console.error(err));
}

async function downloadImage(imageSrc, fileName) {
    try {
        const response = await fetch(imageSrc);
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (err) {
        console.error('Download failed', err);
    }
}

function resetPayBtn() {
    const btn = document.getElementById('submit-pay-btn');
    if (btn) {
        btn.disabled = false;
        btn.textContent = 'Pay Now';
    }
}

function showGetPayBox() {
    const getColorings = document.querySelector('.quiz__get-colorings');

    getColorings.addEventListener('click', (e) => {
        e.preventDefault();

        const resultCountP = document.querySelectorAll('.result__items .result__gitem').length;
        const resultCount = document.querySelectorAll(
            '.result__items .result__item:not(.result__gitem)'
        ).length;

        if (!resultCountP && resultCount) {
            showPayForImages(true, 'download');
        } else if (resultCountP && resultCount) {
            showPayForImages(true, 'get');
        } else if (!resultCount && resultCountP) {
            showPayForImages(true, 'get');
        }
    })
}

function getPriceBySlug(slug) {
    const pColoring = ajaxData.pColoring;

    for (const key in pColoring) {
        if (pColoring[key].slug === slug) {
            if (pColoring[key].price) {
                return Number(pColoring[key].price);
            }
        }
    }

    return 1;
}

function bindGitemClicks() {
    const resultBox = document.querySelector('.uploader__generate .result__items');

    if (!resultBox) return;

    resultBox.addEventListener('click', (e) => {
        const gitem = e.target.closest('.result__gitem');

        if (gitem) {
            e.preventDefault();
            const getColoringsBtn = document.querySelector('.quiz__get-colorings');
            if (getColoringsBtn) {
                getColoringsBtn.click();
            }
        }
    });
}