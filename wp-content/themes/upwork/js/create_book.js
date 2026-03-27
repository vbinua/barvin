(function ($) {
    $('#next_step_auth').on('click', function (e) {
        e.preventDefault();
        $('.authorization').addClass('active');
    })

    $('input[type=text]').attr('maxlength', '30');

    function setCookie(value) {
        document.cookie = "currentBook =" + value + "; path=/";
    }

    function deleteCookie(name) {
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/;';
    }

    function isValidBookForm() {
        let form = document.querySelector(".quiz__body form");
        let pristine = new Pristine(form);

        let elems = document.querySelectorAll("input[type='file']");

        elems.forEach(elem => {
            pristine.addValidator(elem, function (value) {
                let id = elem.getAttribute('data-atach-id');
                let req = elem.getAttribute('data-required');
                if (req == 'true') {
                    if (id != '0') {
                        return true
                    }
                    return false;
                }
                return true;

            }, "The field is required", 2, true);
        })

        let result = pristine.validate();
        return result;
    }

    //Create book
    function sendBookData({answers, step, termId, title, postId = 0, trigger}, callback) {
        const action = (trigger === 'quiz__finish') ? 'create_book_success' : 'create_book';

        const uestId = localStorage.getItem('guest_id');

        $.ajax({
            url: ajaxData.ajaxUrl,
            type: 'POST',
            data: {
                action: action,
                answers: Object.assign({}, answers),
                step: step ?? 1,
                termId: termId,
                title: title,
                postId: postId,
                trigger: trigger,
                guestId: uestId
            },
            success: function (response) {
                setTimeout(() => {
                    $('.notification-successful').addClass('hide');
                }, 2000);
                setTimeout(() => {
                    $('.notification-successful').removeClass('active');
                    $('.notification-successful').removeClass('hide');
                }, 3000);

                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (trigger === 'quiz__save' && !postId) location.href = response.postUrl;
                if (trigger === 'quiz__finish' && !postId) location.href = response.postUrl;
                if (trigger === 'prev_step') location.href = response.prevUrl;
                if (trigger === 'next_step') location.href = response.nextUrl;

                if (callback) callback();
            },
            error: function () {
                $('.notification-error').addClass('active');
                setTimeout(() => {
                    $('.notification-error').addClass('hide');
                }, 2000);
                setTimeout(() => {
                    $('.notification-error').removeClass('active');
                    $('.notification-error').removeClass('hide');
                }, 3000);
            }
        });
    }

    function seveBookData({answers, step, termId, title, postId = 0, trigger}, callback) {
        $.ajax({
            url: ajaxData.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'create_book_success',
                answers: Object.assign({}, answers),
                step: step ?? 1,
                termId: termId,
                title: title,
                postId: postId,
                trigger: trigger
            },
            success: function (response) {
                if (response.postPreview) {
                    const previewButton = $(`
                    <div class="navigation">
                        <a href="${response.postPreview}" data-step="1" class="navigation__right hide">
                            Preview
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.03393 0L5.06498 0.974515L9.37681 5.31093H0L0 6.68912L9.37681 6.68912L5.06498 11.0255L6.03393 12L12 5.99998L6.03393 0Z" fill="#F9F8F4"></path>
                            </svg>
                        </a>
                    </div>
                `);
                    $('.quiz__stap_navigation_box').empty().append(previewButton);
                }

                const body = document.body;
                if (body.classList.contains('logged-in')) {
                    $('.quiz__stap_navigation .navigation__right').removeClass('hide');
                } else {
                    $('.navigation__authentication .navigation__right_authentication').removeClass('hide');
                }

                $(document).ready(function () {
                    if ($('body').hasClass('logged-in')) return;

                    $('.authorization .authorization__box .authorization__header .authorization__title').empty().prepend(`
                            Authorization Required
                            <span class="authorization__description">
                                To continue, please sign in or create an account.
                            </span>
                        `);
                });

            },
            error: function () {
                $('.notification-error').addClass('active');
                setTimeout(() => {
                    $('.notification-error').addClass('hide');
                }, 2000);
                setTimeout(() => {
                    $('.notification-error').removeClass('active');
                    $('.notification-error').removeClass('hide');
                }, 3000);
            }
        });
    }

    function update_book(trigger, steps = 0, callback) {
        let fields = document.querySelectorAll('.body__form input'),
            selects = document.querySelectorAll('.body__form select'),
            answers = [],
            termId = document.querySelector('.quiz').getAttribute('data-term-id'),
            title = document.querySelector('#header__title').value;

        fields.forEach(field => {
            let type = field.getAttribute('type');
            answers[field.getAttribute('id')] = {};
            if (type === 'file') {
                answers[field.getAttribute('id')].type = 'file';
                answers[field.getAttribute('id')].value = field.getAttribute('data-atach-id');
            } else {
                answers[field.getAttribute('id')].type = 'text';
                answers[field.getAttribute('id')].value = field.value;
            }
        });

        selects.forEach(field => {
            answers[field.getAttribute('id')] = {};
            answers[field.getAttribute('id')].type = 'text';
            answers[field.getAttribute('id')].value = field.options[field.selectedIndex].text;
        });

        let url = new URL(window.location.href);
        let step = url.searchParams.get('step');

        sendBookData({
            answers: answers,
            step: step,
            termId: termId,
            title: title,
            postId: url.searchParams.get('edit-book') ?? 0,
            trigger: trigger
        }, callback);
    }

    function activeThirdStep() {
        if (document.querySelector('.body__form input')) {
            let fields = document.querySelectorAll('.body__form input'),
                selects = document.querySelectorAll('.body__form select'),
                answers = [],
                termId = document.querySelector('.quiz').getAttribute('data-term-id'),
                title = document.querySelector('#header__title').value;

            fields.forEach(field => {
                let type = field.getAttribute('type');
                answers[field.getAttribute('id')] = {};
                if (type === 'file') {
                    answers[field.getAttribute('id')].type = 'file';
                    answers[field.getAttribute('id')].value = field.getAttribute('data-atach-id');
                } else {
                    answers[field.getAttribute('id')].type = 'text';
                    answers[field.getAttribute('id')].value = field.value;
                }
            });

            selects.forEach(field => {
                answers[field.getAttribute('id')] = {};
                answers[field.getAttribute('id')].type = 'text';
                answers[field.getAttribute('id')].value = field.options[field.selectedIndex].text;
            });

            let url = new URL(window.location.href);
            let step = url.searchParams.get('step');
            let postId = url.searchParams.get('edit-book') ?? 0;

            if (!postId) {
                sendBookData({
                    answers: answers,
                    step: step,
                    termId: termId,
                    title: title,
                    postId: url.searchParams.get('edit-book') ?? 0,
                    trigger: 'next_step'
                }, () => {
                    setTimeout(() => {
                        $('.quiz-edit').removeClass('hide');
                        $('.main-loader').addClass('hide');
                    }, 2000);
                });
            }
        }

        $(document).ready(function () {
            let url = new URL(window.location.href);
            let step = url.searchParams.get('step');
            let postId = url.searchParams.get('edit-book');

            if (step === '3' || postId) {
                $('.quiz-edit').removeClass('hide');
                $('.main-loader').addClass('hide');
                setTimeout(() => {
                    $('.quiz-edit').removeClass('hide');
                }, 1000);
            }
        });
    }

    activeThirdStep();

    function checkUploadImages() {
        const body = document.body;

        if (body.classList.contains('logged-in')) return;

        const LIMIT = 3;

        document.addEventListener('click', function (e) {

            const uploadBlock = e.target.closest('.quiz__upload');
            if (!uploadBlock) return;

            const quizImages = document.querySelector('.quiz-images');
            if (!quizImages) return;

            const blocks = quizImages.querySelectorAll('.quiz-image-item');

            if (blocks.length > LIMIT) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showUploadLimitPopup();
                return false;
            }

        }, true);

        function showUploadLimitPopup() {
            const popup = document.getElementById('uploadLimitPopup');
            if (!popup) return;

            const closeBtn = popup.querySelector('.closeBtn');
            if (!closeBtn) {
                return;
            }

            popup.classList.remove('hide');

            popup.querySelector('.upload-limit-popup__btn')
                ?.addEventListener('click', () => {
                    popup.classList.add('hide');
                }, {once: true});

            closeBtn.addEventListener('click', () => {
                popup.classList.add('hide');
            });
        }
    }

    checkUploadImages();

    $('.quiz__uploud_file').on('change', function (e) {
        if ($(this).parent('div').hasClass('has-danger')) {
            $(this).parent('div').removeClass('has-danger');
            $(this).parent('div').find('.pristine-error').remove();
        }
    })

    $('.quiz__save').on('click', function (e) {
        e.preventDefault();
        let valid = isValidBookForm();

        let count_step = $(this).attr('data-steps');

        if (!valid) {
            update_book('quiz__save');
        } else {
            update_book('quiz__finish', count_step);
        }
        $([document.documentElement, document.body]).animate({
            scrollTop: $(".has-danger").offset().top
        }, 100);
        $('.notification-error span').text('Please fill the gaps')
        $('.notification-error').addClass('active');
        setTimeout(function () {
            $('.notification-error').addClass('hide');
        }, 2000);

        setTimeout(function () {
            $('.notification-error').removeClass('active');
            $('.notification-error').removeClass('hide');
        }, 3000);
    })

    $('#prev_step').on('click', function (e) {
        e.preventDefault();
        let valid = isValidBookForm();
        if (valid) {
            update_book('prev_step');
        } else {
            $([document.documentElement, document.body]).animate({
                scrollTop: $(".has-danger").offset().top
            }, 100);
            $('.notification-error span').text('Please fill the gaps')
            $('.notification-error').addClass('active');
            setTimeout(function () {
                $('.notification-error').addClass('hide');
            }, 2000);

            setTimeout(function () {
                $('.notification-error').removeClass('active');
                $('.notification-error').removeClass('hide');
            }, 3000);
        }
    })

    $('#next_step').on('click', function (e) {
        e.preventDefault();
        let valid = isValidBookForm();
        if (valid) {
            update_book('next_step');
        } else {
            $([document.documentElement, document.body]).animate({
                scrollTop: $(".has-danger").offset().top
            }, 100);
            $('.notification-error span').text('Please fill the gaps')
            $('.notification-error').addClass('active');
            setTimeout(function () {
                $('.notification-error').addClass('hide');
            }, 2000);

            setTimeout(function () {
                $('.notification-error').removeClass('active');
                $('.notification-error').removeClass('hide');
            }, 3000);
        }
    })

    function getUrlParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    //Save image
    $('#quizForm').on('change', '.quiz__uploud_file', function (event) {
        const files = this.files.length;
        const file = event.target.files[0];
        const postId = getUrlParam('edit-book');
        const fieldId = $(this).attr('data-attach-id');
        const quizSlug = document.querySelector('.quiz-edit').getAttribute('data-quiz-slug') ?? '';

        if (files > 1) {
            $('.notification-caution').addClass('active');
            setTimeout(() => {
                $('.notification-caution').removeClass('active');
            }, 5000);
            $('.notification-caution span').text('Choose only 1 image!');
            return;
        }

        const currentItem = $(this).closest('.quiz-image-item');
        const loader = currentItem.find('.quiz-image-loader');
        const preview = currentItem.find('.quiz-image-preview .preview__box');
        const inputWrapper = currentItem.find('.quiz__upload');

        $('.quiz__stap_navigation .navigation__right').addClass('hide');

        if (!document.body.classList.contains('logged-in')) {
            $('.navigation__right_authentication').addClass('hide');
        }

        const sendFile = (fileData) => {
            let form_data = new FormData();
            form_data.append(fileData instanceof File ? 'file' : 'blob', fileData);
            form_data.append('name', file.name);
            form_data.append('type', file.type);
            form_data.append('action', 'save_image');
            form_data.append('post_id', postId);
            form_data.append('id', fieldId);
            form_data.append('quizSlug', quizSlug);

            inputWrapper.find('svg').css('display', 'flex');
            loader.removeClass('hide');

            $.ajax({
                url: ajaxData.ajaxUrl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
                    inputWrapper.find('svg').css('display', 'none');
                    inputWrapper.find('.quiz__upload_text').text(response.data.filename);
                    preview.removeClass('hide');
                    preview.html(`<img src="${response.data.original_url}" style="max-width:120px">`);

                    currentItem.find('.quiz__uploud_file').prop('disabled', true);

                    const resultBox = currentItem.find('.quiz-image-preview-result .preview__box');
                    showFullImage();
                    waitForResult(postId, fieldId, resultBox, loader);
                },
                error: function (res) {
                    inputWrapper.find('svg').css('display', 'none');
                    loader.addClass('hide');
                }
            });
        };

        if (file.size > 1024 * 1024 * 2) {
            resizeImage(file, 1024 * 1024 * 2, function (resizedFile) {
                sendFile(resizedFile);
            });
        } else {
            sendFile(file);
        }
    });

    function waitForResult(postId, fieldId, resultBox, loader, maxAttempts = 300) {
        let attempts = 0;
        const timer = setInterval(() => {
            attempts++;
            $.ajax({
                url: ajaxData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'check_coloring_result',
                    post_id: postId,
                    field_id: fieldId
                },
                success: function (response) {
                    if (response.success && response.data?.url) {
                        loader.addClass('hide');
                        resultBox.removeClass('hide');
                        resultBox.html(`<img src="${response.data.url}" style="max-width:120px">`);
                        clearInterval(timer);
                        showFullImage();

                        let fields = document.querySelectorAll('.body__form input'),
                            selects = document.querySelectorAll('.body__form select'),
                            answers = [],
                            termId = document.querySelector('.quiz').getAttribute('data-term-id'),
                            title = document.querySelector('#header__title').value;

                        fields.forEach(field => {
                            let type = field.getAttribute('type');
                            answers[field.getAttribute('id')] = {};
                            if (type === 'file') {
                                answers[field.getAttribute('id')].type = 'file';
                                answers[field.getAttribute('id')].value = field.getAttribute('data-atach-id');
                            } else {
                                answers[field.getAttribute('id')].type = 'text';
                                answers[field.getAttribute('id')].value = field.value;
                            }
                        });

                        selects.forEach(field => {
                            answers[field.getAttribute('id')] = {};
                            answers[field.getAttribute('id')].type = 'text';
                            answers[field.getAttribute('id')].value = field.options[field.selectedIndex].text;
                        });

                        let url = new URL(window.location.href);
                        let step = url.searchParams.get('step');
                        let postId = url.searchParams.get('edit-book') ?? 0;

                        seveBookData({
                            answers: answers,
                            step: step,
                            termId: termId,
                            title: title,
                            postId: postId,
                            trigger: 'quiz__finish'
                        }, () => {
                        });

                        if (!document.body.classList.contains('logged-in')) {
                            $('.navigation__right').removeClass('hide');
                        }

                    } else if (attempts >= maxAttempts) {
                        loader.addClass('hide');
                        clearInterval(timer);
                        console.warn(`Coloring result not ready for field ${fieldId}`);
                    }
                },
                error: function (error) {
                    loader.addClass('hide');
                    clearInterval(timer);
                    console.error('Error checking coloring result:', error);
                }
            });
        }, 3000);
    }

    function duplicateField() {
        const container = document.querySelector('.quiz-images');

        let attachIdCounter = 0;
        document.querySelectorAll('.quiz-image-item').forEach(item => {
            const id = parseInt(item.dataset.atachId);
            if (!isNaN(id) && id >= attachIdCounter) {
                attachIdCounter = id + 1;
            }
        });

        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('quiz__uploud_file')) return;

            const input = e.target;
            const currentItem = input.closest('.quiz-image-item');
            const uploadText = currentItem.querySelector('.quiz__upload_text');
            const loader = currentItem.querySelector('.quiz-image-loader');

            if (input.files.length) {
                uploadText.textContent = input.files[0].name;
                loader?.classList.remove('hide');
            }

            if (currentItem === container.lastElementChild) {

                const clone = currentItem.cloneNode(true);

                const newId = attachIdCounter++;
                clone.dataset.atachId = newId;

                const cloneInput = clone.querySelector('.quiz__uploud_file');
                cloneInput.value = '';
                cloneInput.setAttribute('name', `images[${newId}]`);
                cloneInput.setAttribute('data-attach-id', newId);

                const cloneStatus = clone.querySelector('input[type="hidden"]');
                cloneStatus.setAttribute('name', `status[${newId}]`);
                cloneStatus.value = 'pending';

                clone.querySelector('.quiz__upload_text').textContent = 'Drop your image here';

                clone.querySelector('.quiz-image-loader')?.classList.add('hide');

                const preview = clone.querySelector('.quiz-image-preview .preview__box');
                if (preview) preview.innerHTML = '';

                const result = clone.querySelector('.quiz-image-preview-result .preview__box');
                if (result) result.innerHTML = '';

                container.appendChild(clone);
            }
        });
    }

    duplicateField();

    function showFullImage() {
        const modal = document.getElementById('imageModal');
        if (!modal) {
            return;
        }

        const modalImg = modal.querySelector('img');
        if (!modalImg) {
            return;
        }

        const closeBtn = modal.querySelector('.closeBtn');
        if (!closeBtn) {
            return;
        }

        if (document.querySelector('.preview__box img')) {
            document.querySelectorAll('.quiz-image-preview, .quiz-image-preview-result').forEach(wrapper => {
                const img = wrapper.querySelector('.preview__box img');
                if (!img) return;

                wrapper.addEventListener('click', () => {
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
    }

    showFullImage();

    function resizeImage(file, maxSize, callback) {
        var reader = new FileReader();
        reader.onload = function () {
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var width = img.width;
                var height = img.height;
                var quality = 0.25;
                var ratio = width / height;
                if (width > height) {
                    if (width > maxSize) {
                        width = maxSize;
                        height = width / ratio;
                    }
                } else {
                    if (height > maxSize) {
                        height = maxSize;
                        width = height * ratio;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                canvas.toBlob(function (blob) {
                    callback(blob);
                }, 'image/jpeg', quality);
            };
            img.src = reader.result;
        };
        reader.readAsDataURL(file);
    }

    let editButtons = document.querySelectorAll('.edited__edit');
    editButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            let bookId = button.getAttribute('data-book-id');
            let path = button.getAttribute('href');
            setCookie(bookId);
            document.location = path;
        });
    });

    $('.header__edit').on('click', function () {
        $('.header__title').focus();
    })

    const fullNameEle = document.getElementById('fullName');
    const editEle = document.getElementById('edit');

    if (editEle) {
        editEle.addEventListener('click', function (e) {
            // Focus on the full name element
            fullNameEle.focus();

            // Move the cursor to the end
            const length = fullNameEle.value.length;
            fullNameEle.setSelectionRange(length, length);
        });
    }

    $('#firstSlide').hide();


    // Slider on a single post page
    const slider = $('.book');


    if (slider) {
        slider.slick({
            infinite: false,
            dots: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            speed: 100,
            nextArrow: $('#nextSlide'),
            prevArrow: $('#prevSlide')
        });

        $('.navigation__count').css('opacity', '0');

        $('#lastSlide').on('click', function (e) {
            e.preventDefault();
            let slides = $('.navigation__count').attr('data-count-pages');
            slides = parseInt(slides);
            slider.slick('slickGoTo', slides / 2 + 1);
        })

        $('#firstSlide').on('click', function (e) {
            e.preventDefault();
            slider.slick('slickGoTo', 0);
        })

        slider.on('afterChange', function (event, slick, currentSlide, nextSlide) {
            $('#firstSlideNumber').text((currentSlide) * 2 - 1);
            $('#lastSlideNumber').text((currentSlide) * 2);
            let countSlides = $(".book").slick("getSlick").slideCount - 1;
            if (currentSlide == 0) {
                $('#firstSlide').hide();
            } else {
                $('#firstSlide').show();
            }

            if (currentSlide == countSlides) {
                $('#lastSlide').hide();
            } else {
                $('#lastSlide').show();
            }

            if (currentSlide == 0) {
                $('.navigation__count').css('opacity', '0');
            } else {
                $('.navigation__count').css('opacity', '1');
            }
        });
    }

    $('.notification__exit').on('click', function (e) {
        $(this).parent().removeClass('active')
    })


    // change required status according to conditionals

    function falseRequiredStutus(id) {
        const input = $(id);
        input.removeAttr('required');
        input.parent().addClass('blocked');
        input.siblings('.pristine-error ').remove();
        input.parent().removeClass('has-danger')
    }

    function trueRequiredStutus(id) {
        const input = $(id);
        input.attr('required', 'true');
        input.parent().removeClass('blocked')
    }

    function falseRequiredImage(id) {
        const input = $(id);
        input.removeAttr('data-required');
        input.removeAttr('required');
        input.parent().parent().parent().addClass('blocked')
        input.parent().removeClass('has-danger')
        input.siblings('.pristine-error ').remove();
    }

    function trueRequiredImage(id) {
        const input = $(id);
        input.attr('data-required', 'true');
        input.parent().parent().parent().removeClass('blocked')
    }


    $('.term-new-baby #answer_14').on('change', function () {
        if ($(this).val() == 'No') {
            falseRequiredStutus('#answer_15');
            falseRequiredImage('#answer_16');
        } else {
            trueRequiredStutus('#answer_15');
            trueRequiredImage('#answer_16');
        }
    })

    $('.term-new-baby #answer_17').on('change', function () {
        if ($(this).val() == 'No') {
            falseRequiredStutus('#answer_18');
            falseRequiredImage('#answer_19');
        } else {
            trueRequiredStutus('#answer_18');
            trueRequiredImage('#answer_19');
        }
    })


    if ($('.term-new-baby #answer_14').length) {
        if ($('.term-new-baby #answer_14').val() == 'No') {
            falseRequiredStutus('#answer_15');
            falseRequiredImage('#answer_16');
        } else {
            trueRequiredStutus('#answer_15');
            trueRequiredImage('#answer_16');
        }
    }
    if ($('.term-new-baby #answer_17').length) {
        if ($('.term-new-baby #answer_17').val() == 'No') {
            falseRequiredStutus('#answer_18');
            falseRequiredImage('#answer_19');
        } else {
            trueRequiredStutus('#answer_18');
            trueRequiredImage('#answer_19');
        }
    }


    //    The Potty Book conditions
    $('.term-the-potty #answer_10').on('change', function () {
        if ($(this).val() == 'No') {
            falseRequiredStutus('#answer_11');
            falseRequiredImage('#answer_12');
        } else {
            trueRequiredStutus('#answer_11');
            trueRequiredImage('#answer_12');
        }
    })

    if ($('.term-the-potty #answer_10').length) {
        if ($('#answer_10').val() == 'No') {
            falseRequiredStutus('#answer_11');
            falseRequiredImage('#answer_12');
        } else {
            trueRequiredStutus('#answer_11');
            trueRequiredImage('#answer_12');
        }
    }

    $('.term-the-potty #answer_13').on('change', function () {
        if ($(this).val() == 'No') {
            falseRequiredImage('#answer_14');
        } else {
            trueRequiredImage('#answer_14');
        }
    })

    if ($('.term-new-baby #answer_13').length) {
        if ($('#answer_13').val() == 'No') {
            falseRequiredImage('#answer_14');
        } else {
            trueRequiredImage('#answer_14');
        }
    }


    //    Starting school Book conditions
    $('.term-starting-school #answer_4').on('change', function () {
        if ($('#answer_4').val() == 'At home') {
            trueRequiredStutus('#answer_5');
            trueRequiredImage('#answer_6');
            falseRequiredStutus('#answer_7');
            falseRequiredImage('#answer_8');
        } else {
            falseRequiredStutus('#answer_5');
            falseRequiredImage('#answer_6');
            trueRequiredStutus('#answer_7');
            trueRequiredImage('#answer_8');

        }
    })

    if ($('.term-starting-school #answer_4').length) {
        if ($('#answer_4').val() == 'At home') {
            trueRequiredStutus('#answer_5');
            trueRequiredImage('#answer_6');
            falseRequiredStutus('#answer_7');
            falseRequiredImage('#answer_8');
        } else {
            falseRequiredStutus('#answer_5');
            falseRequiredImage('#answer_6');
            trueRequiredStutus('#answer_7');
            trueRequiredImage('#answer_8');
        }
    }

    //    Wedding Book conditions
    $('.term-wedding_book #answer_4').on('change', function () {
        if ($('#answer_4').val() == 'Ring bearer') {
            trueRequiredStutus('#answer_5');
        } else {
            falseRequiredStutus('#answer_5')
        }
    })

    $('.term-wedding_book #answer_14').on('change', function () {
        if ($('#answer_14').val() == 'No') {
            falseRequiredImage('#answer_15');
        } else {
            trueRequiredImage('#answer_15');
        }
    })

    if ($('.term-wedding_book #answer_14').length) {
        if ($('#answer_14').val() == 'No') {
            falseRequiredImage('#answer_15');
        } else {
            trueRequiredImage('#answer_15');
        }
    }

    if ($('.term-wedding_book #answer_4').length) {
        if ($('#answer_4').val() == 'Ring bearer') {
            trueRequiredStutus('#answer_5');
        } else {
            falseRequiredStutus('#answer_5');
        }
    }

    $('.term-wedding_book #answer_7').on('change', function () {
        if ($('#answer_7').val() == 'No') {
            trueRequiredStutus('#answer_8');
            trueRequiredImage('#answer_9');
        } else {
            falseRequiredStutus('#answer_8')
            falseRequiredImage('#answer_9')
        }
    })

    /*if ($('.term-wedding_book #answer_7').length) {
        if ($('#answer_6').val() == 'No') {
            trueRequiredImage('#answer_8');
            trueRequiredImage('#answer_9');
        } else {
            falseRequiredImage('#answer_8')
            falseRequiredImage('#answer_9')
        }
    }*/

    $('.term-wedding_book #answer_13').on('change', function () {
        if ($('#answer_13').val() == 'Yes') {
            trueRequiredImage('#answer_14');
        } else {
            falseRequiredImage('#answer_14')
        }
    })

    if ($('.term-wedding_book #answer_13').length) {
        if ($('#answer_13').val() == 'Yes') {
            trueRequiredImage('#answer_14');
        } else {
            falseRequiredImage('#answer_14');
        }
    }

    // if ($('.term-starting-school #answer_16').length) {
    //     if ($('.term-starting-school #answer_16').val() == 'No') {
    //         trueRequiredStutus('#answer_17');
    //     } else {
    //         falseRequiredStutus('#answer_17');
    //     }
    // }
    // $('.term-starting-school #answer_16').on('change', function () {
    //     if ($('.term-starting-school #answer_16').val() == 'No') {
    //         trueRequiredStutus('#answer_17');
    //     } else {
    //         falseRequiredStutus('#answer_17');
    //     }
    // })


    let elems = document.querySelectorAll(".quiz__body input[type='file']");
    if (elems) {
        elems.forEach(elem => {
            elem.removeAttribute('required')
        })
    }
    $('.remove_image').on('click', function () {
        let input = $(this).prev('input')[0];
        let post_id = $(this).attr('data-post-id')
        let image_id = $(input).attr('data-atach-id')
        let id = $(input).attr('id')
        $(this).text('');
        $(this).parent().find('input').val('');
        $(this).parents('.quiz__upload').find('.quiz__upload_text').text('Drop your image here');
        $(this).parents('.quiz__upload').find('.quiz__upload_text').prev().show();
        $.ajax({
            url: ajaxData.ajaxUrl,
            type: 'POST',
            data: {
                'action': 'delete_image',
                'image_id': image_id,
                'post_id': post_id,
                'id': id
            },
            success: function (response) {
            },
            error: function (error) {
            }
        });
    });

    $('.edited__delete').on('click', function (e) {
        e.preventDefault();
        // let id = $(this).attr('data-book-id');
        // $.ajax({
        //     url: '/wp-admin/admin-ajax.php',
        //     type: 'POST',
        //     data: {
        //         'action': 'delete_book_from_cart',
        //         'id' : id
        //     },
        //     success: function (response) {
        //     },
        //     error: function (error) {
        //     }
        // });

        let id = $(this).attr('data-book-id');
        const template = `
        <div class="remove-modal" data-book-id='${id}'>
        <div class="remove-modal__bg"></div>
        <div class="remove-modal__close remove-cancel">
        Close
        <span><img src="https://upcoming.intex.agency/wp-content/themes/upwork/img/close.svg" alt="close"></span>
        </div>
        <div class="remove-modal__content">
          <div class="remove-modal__title">Are you sure?</div>
          <div class="remove-modal__actions">
          <button class="remove-modal__actions-item remove-cancel">Cancel</button>
          <button class="remove-modal__actions-item remove-accept">Delete</button>
          </div>
        </div>
        </div>
        `
        const element = document.createElement('div')
        element.setAttribute('id', 'delete-modal');
        element.innerHTML = template
        document.body.appendChild(element)
        setTimeout(() => {
            $('.remove-modal').addClass('active')
        }, 10)
    });

    $('body').on('click', '.remove-cancel', function (e) {
        e.preventDefault()
        $('.remove-modal').removeClass('active')
        setTimeout(() => {
            $('#delete-modal').remove()
        }, 400)
    })
    $('body').on('click', '.remove-accept', function (e) {
        e.preventDefault()
        let id = $('.remove-modal').attr('data-book-id');

        $.ajax({
            url: ajaxData.ajaxUrl,
            type: 'POST',
            data: {
                'action': 'delete_book_from_cart',
                'id': id
            },
            success: function (response) {
                $('.remove-modal').removeClass('active')
                $('.remove-modal').remove()
                location.reload()
            },
            error: function (error) {
            }
        });
    })


}
(jQuery));
