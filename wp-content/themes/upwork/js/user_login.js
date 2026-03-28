(function ($) {
    function getUrlParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    $('.authorization__forgot').on('click', function () {
        $('.authorization__body.active').removeClass('active');
        $('.authorization__body:nth-child(4)').addClass('active');
    });

    // logout function
    $('.header__logout').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'user_logout1',
            },
            type: 'POST',
            success: function (response) {
                location.replace('https://barvinart.com');
            }
        });
    })

    // reset pass functions
    $('#send_pass').on('click', function (e) {
        e.preventDefault();
        let email = $('#user_email1').val();

        const button = $(this)
        const buttonLoader = $(this).find('.authorization__button-loader')
        button.attr('disabled', true)
        buttonLoader.addClass('active')

        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'user_reset_pass',
                'email': email,
            },
            type: 'POST',
            success: function (response) {
                $('.error__text').remove();
                $('.error').removeClass('error');
                response = JSON.parse(response);

                if (response.length > 0) {
                    for (let i = 0; i < response.length; i++) {
                        $('#' + response[i][0]).after('<span class="error__text">' + response[i][1] + '</span>');
                        $('#' + response[i][0]).addClass('error');
                    }
                } else {
                    location.reload();
                }
            },
            complete: function (data) {
                setTimeout(() => {
                    button.attr('disabled', false)
                    buttonLoader.removeClass('active')
                }, 800)
            }
        });
    });


    // login functions
    $('#login_user').on('click', function (e) {
        e.preventDefault();

        let email = $('#user_email').val();
        let password = $('#user_pass').val();

        const button = $(this);
        const buttonLoader = $(this).find('.authorization__button-loader');
        const guestId = localStorage.getItem('guest_id');
        const termId = document.querySelector('.quiz-edit')?.getAttribute('data-term-id') ?? '';
        const postId = getUrlParam('edit-book');

        button.attr('disabled', true);
        buttonLoader.addClass('active');

        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'user_login_action',
                'email': email,
                'password': password,
                'guestId': guestId,
                'termId': termId,
                'postId': postId,
            },
            type: 'POST',
            success: function (response) {
                $('.error__text').remove();
                $('.error').removeClass('error');
                response = JSON.parse(response);
                if (response.length > 0) {
                    for (let i = 0; i < response.length; i++) {
                        $('#' + response[i][0]).after('<span class="error__text">' + response[i][1] + '</span>');
                        $('#' + response[i][0]).addClass('error');
                    }
                } else {
                    if (window.location.href.includes('templates')) {
                        $('#next_step_auth').remove();
                        $('#next_step').css('display', 'flex');
                        $('.authorization').removeClass('active');

                        if (localStorage.getItem('guest_id')) {
                            localStorage.removeItem('guest_id');
                        }

                        if ($('.quiz__stap_navigation .navigation__right_authentication').length) {
                            $('.quiz__stap_navigation .navigation__right_authentication').addClass('hide');
                        }

                        if ($('.quiz__stap_navigation .navigation__right').length) {
                            $('.quiz__stap_navigation .navigation__right').removeClass('hide');
                            $('.quiz__stap_navigation .navigation__right').click();

                            const link = $('.quiz__stap_navigation .quiz__stap_navigation_box .navigation__right').attr('href');

                            if (link) {
                                window.location.href = link;
                            }
                        }

                    } else {
                        location.reload();
                    }
                }
            },
            complete: function (data) {
                setTimeout(() => {
                    button.attr('disabled', false)
                    buttonLoader.removeClass('active')
                }, 800)
            }
        });
    });

    // register functions

    let form = document.getElementById("reg_form"),
        pristine = new Pristine(form);

    let elem = document.getElementById("confirm_password");

    pristine.addValidator(elem, function (value) {

        let elem2 = document.getElementById("password");

        if (value == elem2.value) {
            return true;
        }

        return false;
    }, "Password and the Confirm Password fields should be equals", 2, false);


    $('#register_submit').on('click', function (e) {
        e.preventDefault();
        let valid = pristine.validate();

        const button = $(this)
        const buttonLoader = $(this).find('.authorization__button-loader');
        const guestId = localStorage.getItem('guest_id');
        const termId = document.querySelector('.quiz-edit')?.getAttribute('data-term-id') ?? '';
        const postId = getUrlParam('edit-book');

        let name = $('#first_name').val();
        let last_name = $('#last_name').val();
        let email = $('#email').val();
        let password = $('#password').val();

        if (valid) {
            button.attr('disabled', true)
            buttonLoader.addClass('active')
            $.ajax({
                url: ajaxData.ajaxUrl,
                data: {
                    'action': 'user_register_action',
                    'email': email,
                    'password': password,
                    'name': name,
                    'last_name': last_name,
                    'guestId': guestId,
                    'termId': termId,
                    'postId': postId,
                },
                type: 'POST',
                success: function (response) {
                    $('.error').remove();
                    if (response.status != 'error') {
                        if (window.location.href.includes('templates')) {
                            $('#next_step_auth').remove();
                            $('#next_step').css('display', 'flex');
                            $('.authorization').removeClass('active');

                        } else {
                            location.reload();
                        }

                        if (localStorage.getItem('guest_id')) {
                            localStorage.removeItem('guest_id');
                        }

                        if ($('.quiz__stap_navigation .navigation__right_authentication').length) {
                            $('.quiz__stap_navigation .navigation__right_authentication').addClass('hide');
                        }

                        if ($('.quiz__stap_navigation .navigation__right').length) {
                            $('.quiz__stap_navigation .navigation__right').removeClass('hide');
                            $('.quiz__stap_navigation .navigation__right').click();

                            const link = $('.quiz__stap_navigation .quiz__stap_navigation_box .navigation__right').attr('href');

                            if (link) {
                                window.location.href = link;
                            }
                        }

                    } else {
                        var emailField = $('#email');
                        if (emailField.length > 0) {
                            var errorDivs = emailField.nextAll('.pristine-error');

                            errorDivs.each(function () {
                                var errorDiv = $(this);

                                errorDiv.text(response.data[0]);

                                errorDiv.show();

                                errorDiv.closest('label').addClass('has-danger');
                            });
                        }
                    }

                },
                complete: function (data) {
                    setTimeout(() => {
                        button.attr('disabled', false)
                        buttonLoader.removeClass('active')
                    }, 800)
                }
            });
        }
    });

    $('.header__logout').on('click', function (e) {
        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'user_logout',
            },
            type: 'POST',
            success: function (response) {
                $('.error').remove();
                response = JSON.parse(response);
                location.reload();
            }
        });
    });

    // pop up functions

    const btns = document.querySelectorAll('.open_form');
    const block = document.querySelector('.authorization');
    $('#open_form').on('click', function () {
        block.classList.add('active');
    });
    btns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            block.classList.add('active');
        });
    });

    $('.authorization__close').on('click', function () {
        $('.authorization').removeClass('active');
    });
    $('.authorization__linck').on('click', function () {
        $('.authorization__linck.authorization__linck_active').removeClass('authorization__linck_active');
        $(this).addClass('authorization__linck_active');
        $('.authorization__body.active').removeClass('active');
        $('.authorization__body:nth-child(' + ($(this).index() + 2) + ')').addClass('active');
    });


    // email functions
    let form_mail = document.getElementById("mail_form");
    let infoBlock = document.querySelector('.contact__form_info');
    if (form_mail) {
        let pristine_mail = new Pristine(form_mail);
        $('#send_mail').on('click', function (e) {
            e.preventDefault();
            let valid = pristine_mail.validate();
            let name = $('#your-name').val();
            let email = $('#your-email').val();
            let message = $('#your-message').val();
            if (valid) {
                $.ajax({
                    url: ajaxData.ajaxUrl,
                    data: {
                        'action': 'user_mail',
                        'email': email,
                        'message': message,
                        'name': name,
                    },
                    type: 'POST',
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response) {
                            infoBlock.style.display = 'block';

                            $(form_mail).trigger('reset');
                        }
                    }
                });
            }
        });
    }

    $(document).on('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === "p" || e.charCode === 16 || e.charCode === 112 || e.keyCode === 80)) {
            e.cancelBubble = true;
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        }
    });

}(jQuery));
