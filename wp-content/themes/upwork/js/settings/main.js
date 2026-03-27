(function ($) {
    localStorage.removeItem('couponDiscount');
    /*const isPrint = location.href.includes('?print=true')

    function dataURLtoFile(dataurl, filename) {

        var arr = dataurl.split(','),
            mime = 'application/pdf'
            bstr = atob(arr[1]),
            n = bstr.length,
            u8arr = new Uint8Array(n);

        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }

        return new File([u8arr], filename, {type:mime});
    }*/

    /*setTimeout(async () => {
        if (isPrint) {

            //let book = $("div.quiz__body.quiz__body_book" ).html();
            /!*let book = $('div.quiz__body.quiz__body_book').html();
            let head = $('head');
            console.log(head)*!/
            /!*$.ajax({
                url: '/up_coming/wp-admin/admin-ajax.php',
                data: {
                    'action': 'create_pdf',
                    'book': book,
                },
                type: 'POST',
                success: function (response) {
                    console.log(response)
                }
            });*!/

            const doc = new jsPDF('p', 'pt', [816, 816]);

            const pages = 20;
            let isFirstIteration = true

            for (var i = 0; i < pages; i++){
                const nodes = $('#page_'+i).children()

                if(nodes.length)
                {
                    for (node of nodes)
                    {
                        var options = {
                            quality: 0.99,
                            width: 1088,
                            height: 1088,
                        };
                        if(!isFirstIteration){
                            const pdfPage = await doc.addPage([816, 816])
                            await domtoimage.toPng(node,options)
                                .then(async (dataUrl) => {
                                    await pdfPage.addImage(dataUrl,'png',0,0)
                                })
                                .catch(function (error) {
                                    console.error('oops, something went wrong!', error);
                                });
                        }else{
                            await domtoimage.toPng(node,options)
                                .then(async (dataUrl) => {
                                    await doc.addImage(dataUrl,'png',0,0)
                                })
                                .catch(function (error) {
                                    console.error('oops, something went wrong!', error);
                                });
                        }
                        isFirstIteration = false
                    }


                }
            }*/
            /* //var uri = doc.output('datauristring');
            window.open(doc.output('bloburl'),'_self')

            /!*let file = btoa(doc.output());

              $.ajax({
                  url: '/up_coming/wp-admin/admin-ajax.php',
                  data: {
                      'action': 'create_book',
                      'data' : file,
                  },
                  dataType : 'json',
                  type: 'POST',
                  success: function (response) {
                      console.log(response)
                  }
              });*!/

        }
    }, 1000)*/


    function menu() {
        if (document.querySelector('.header__profile')) {
            const btn = document.querySelector('.header__profile'),
                block = document.querySelector('.header__profile_block');

            document.addEventListener('mouseover', (e) => {
                const blockFlag = e.composedPath().includes(btn);

                if (!blockFlag) {
                    block.style.display = 'none';

                } else {
                    block.style.display = 'block';
                }
            });
        }
    }
    menu();

    function videoPopup() {
      if (document.querySelector('.video__popup')) {
        const block = document.querySelector('.video__popup');
        const btn = document.querySelector('.video__box .video__play');
        const box = document.querySelector('.video__popup .video__popup_block');
        const close = document.querySelector('.video__popup_close');

        btn.addEventListener('click', (e) => {
          e.preventDefault();
          block.style.display = 'block';
          document.querySelector('html').style.overflow = 'hidden';
        });

        close.addEventListener('click', () => {
          const content = box.innerHTML;
          box.innerHTML = '';
          setTimeout(() => {
            box.innerHTML = content;
          }, 1000);
          block.style.display = 'none';
          document.querySelector('html').style.overflow = 'auto';
        });
      }
    }


    function videoBlockPlay() {
      if (document.querySelector('.video__box')) {
        const block = document.querySelector('.video__box .video__box_active');
        const btn = document.querySelector('.video__box .video__play');
       const img = document.querySelector('.video__box img');
        const video = document.querySelector('#myVideo');

        btn.addEventListener('click', (e) => {
          e.preventDefault();
          block.style.display = 'block';
          img.style.display = 'none';
        btn.style.display = 'none';
        video.play();
        });
      }
    }
    videoBlockPlay();



    function ViewDetails() {
        if (document.querySelector('.details__title')) {
            const btn = document.querySelector('.details__title'),
                block = document.querySelector('.details__block'),
                arrow = document.querySelector('.details__arrow');

            btn.addEventListener('click', () => {
                block.classList.toggle('show');
                arrow.classList.toggle('TransformArrow');
            });
        }
    }
    ViewDetails();

    function profileOrders() {
        const btn1 = document.querySelectorAll('.box__navigation_1'),
            btn2 = document.querySelectorAll('.box__navigation_2'),
            btn3 = document.querySelectorAll('.box__navigation_3'),
            btnAll = document.querySelectorAll('.box__navigation'),
            block1 = document.querySelector('.box__table_first'),
            block2 = document.querySelector('.box__table_second'),
            block3 = document.querySelector('.box__table_third'),
            blockAll = document.querySelectorAll('.box__table'),
            linkDraft = document.querySelector('.header__profile_block-item');

        // Cookie
        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires;
        }

        function getCookie(name) {
            var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        let clickedOnDraft = getCookie('Clicked on Draft');

        if (linkDraft) {
            linkDraft.addEventListener('click', (e) => {
                if (!clickedOnDraft) {
                    let clickedOnDraft = true;
                    setCookie('Clicked on Draft', clickedOnDraft, 1);
                }
                choseDraftSection();
            });
        }

        if (document.documentElement.clientWidth < 420) {
            let clickedOnDraft = true;
            setCookie('Clicked on Draft', clickedOnDraft, 1);
        }

        function choseDraftSection() {
            if (clickedOnDraft) {
                btnItems();
                blockItems();
                block2.style.display = 'block';

                btn2.forEach((btn) => {
                    btn.classList.add('box__navigation_active');
                });
            }
        }

        if (document.querySelector('.box__table_first')) {
            function btnItems() {
                btnAll.forEach((item) => {
                    item.classList.remove('box__navigation_active');
                })
            }

            function blockItems() {
                blockAll.forEach((item) => {
                    item.style.display = 'none';
                })
            }

            block1.style.display = 'block';
            btn1.forEach((btn) => {
                btn.classList.add('box__navigation_active');
            });

            btn1.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    btnItems();
                    blockItems();
                    block1.style.display = 'block';

                    btn1.forEach((btn) => {
                        btn.classList.add('box__navigation_active');
                    });
                });
            });

            btn2.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    btnItems();
                    blockItems();
                    block2.style.display = 'block';

                    btn2.forEach((btn) => {
                        btn.classList.add('box__navigation_active');
                    });
                });
            });

            choseDraftSection();

            btn3.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    btnItems();
                    blockItems();
                    block3.style.display = 'block';

                    btn3.forEach((btn) => {
                        btn.classList.add('box__navigation_active');
                    });
                });
            });
        }
    }
    if (document.querySelector('.header__cart')) {
        profileOrders();
    }

    function profileOrdersMob() {
        if (document.querySelector('.box__navigations_mob .box__links')) {

            const btn = document.querySelector('.box__link'),
                block = document.querySelector('#links');

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                block.classList.toggle('showMenu');
                btn.classList.toggle('markButton');
            });
        }
    }
    profileOrdersMob();

    function sliderReview() {
        $('.slider-review').slick({
            dots: true,
            arrows: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            slidesToScroll: 1,
        });
    }
    sliderReview();

    function faq() {

        const block = document.querySelectorAll('.faq');

        let i;

        for (i = 0; i < block.length; i++) {
            block[i].addEventListener("click", function () {
                this.classList.toggle("faq__active");
            });
        }
    }
    faq();

    function setCookie(value) {
        document.cookie = "currentBook =" + value;
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) return match[2];
    }

    function deleteCookie(name) {
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/;';
    };

    function openQuiz(elem) {
        let path = elem.getAttribute('href'),
            bookId = elem.getAttribute('data-term');
        document.location = path;
    }

    if (document.querySelectorAll('.books__block')) {
        let books = document.querySelectorAll('.books__block');

        books.forEach(book => {
            book.addEventListener('click', (e) => {
                e.preventDefault();
                deleteCookie('currentBook');
                openQuiz(book);
            });
        });

    }

    function checkButonsCount() {
        if (document.querySelector('.books__union a')) {
            const btns = document.querySelectorAll('.books__union a'),
                btn = document.querySelector('.books__linck');
            btnsCount = btns.length;

            if (btnsCount <= 3) {
                btn.style.display = 'none';
            }
        }
    }
    checkButonsCount();


    function checkLoginTab() {
        if (document.querySelector('.authorization__linck')) {
            const btnLogin = document.querySelector('.authorization__linck_login'),
                btnRegister = document.querySelector('.authorization__linck_register'),
                block = document.querySelector('.authorization .authorization__box .authorization__footer .authorization__information');

            btnRegister.addEventListener('click', (e) => {
                e.preventDefault();
                block.style.opacity = 1;
            });

            btnLogin.addEventListener('click', (e) => {
                e.preventDefault();
                block.style.opacity = 0;
            });
        }
    }
    checkLoginTab();

    $('#update_information').on('click', function (e) {
        e.preventDefault();
        let first_name = $('#checkout_first_name').val();
        let last_name = $('#checkout_last_name').val();
        let checkout_street = $('#checkout_street').val();
        let checkout_apartment = $('#checkout_apartment').val();
        let checkout_city = $('#checkout_city').val();
        let checkout_postcode = $('#checkout_postcode').val();
        let checkout_phone = $('#checkout_phone').val();
        let email = $('#checkout_email').val();
        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'update_user_info',
                'first_name': first_name,
                'last_name': last_name,
                'checkout_street': checkout_street,
                'checkout_apartment': checkout_apartment,
                'checkout_city': checkout_city,
                'checkout_postcode': checkout_postcode,
                'email': email,
                'checkout_phone': checkout_phone,
            },
            type: 'POST',
            success: function (response) {
                response = JSON.parse(response);
                location.reload();
            }
        });
    });

    $('.select2').select2({
        minimumResultsForSearch: Infinity,
    })
}(jQuery));
