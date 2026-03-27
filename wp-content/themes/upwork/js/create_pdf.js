(function ($) {
    var myPageIsDirty = 0;
    $(document).ready(function () {
        $('#checkout_apartment').change();
    });
    window.addEventListener('beforeunload', function (e) {
        //you implement this logic...
        if (myPageIsDirty) {
            //following two lines will cause the browser to ask the user if they
            //want to leave. The text of this dialog is controlled by the browser.
            e.preventDefault(); //per the standard
            e.returnValue = ''; //required for Chrome
        }
    });
    $('#checkout_postcode').on('input', function (e) {
        e.preventDefault();
        let zip_val = e.target.value.replace(/\D/g, '').match(/(\d{0,5})/);
        e.target.value = !zip_val[2] ? zip_val[1] : '' + zip_val[1];
    });
    let user_state = $('#checkout_apartment').attr('value');
    $('#checkout_apartment option[value="' + user_state + '"]').attr('selected', 'selected');


    $('#checkout_phone').on('input', function (e) {
        e.preventDefault();
        let current_val = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = !current_val[2] ? current_val[1] : '' + current_val[1] + '-' + current_val[2] + (current_val[3] ? '-' + current_val[3] : '');
    });

    const safeStyle = (el, cb) => {
        if (el) cb(el);
    };

    const btnOrder = document.querySelector('#place_order'),
        btnOrderFake = document.querySelector('.btnOrderHide'),
        shippingPrice = document.querySelector('.order__shipping_price'),
        taxPrice = document.querySelector('.order__tax_price'),
        totalPrice = document.querySelector('.order__order__m-total_price'),
        nextStage = document.querySelector('.next-stage'),
        shipping = document.querySelector('.shipping'),
        total = document.querySelector('.order__order__m-total_price-info'),
        totalDefault = document.querySelector('.order__order__m-total_price-default'),
        orderTax = document.querySelector('.order__tax_price-info'),
        orderTaxDefault = document.querySelector('.order__tax_price-default'),
        allRadio = document.querySelectorAll('.shipping label input'),
        firstStep = document.querySelector('.first-step'),
        shippings = document.querySelectorAll('.shipping label input');


    setInterval(() => {
        let inputs = $('.checkout_form input:not(#apartment),select');
        let checkAutocomplete = 1;

        for (let i = 0; i < inputs.length; i++) {
            if (!$(inputs[i]).val()) {
                checkAutocomplete = 0;
                break;
            }
        }

        if (checkAutocomplete) {
            safeStyle(nextStage, el => el.style.opacity = 1);
            safeStyle(shipping, el => el.style.opacity = 1);

            firstStep?.classList.add('step-done');
            nextStage?.classList.add('step-done');

            orderTaxDefault && (orderTaxDefault.style.display = 'none');
            orderTax && (orderTax.style.display = 'block');

            totalDefault && (totalDefault.style.display = 'none');
            total && (total.style.display = 'block');

            btnOrder && (btnOrder.style.display = 'flex');
            btnOrderFake && (btnOrderFake.style.display = 'none');

            $('#coupon').removeClass('btnCouponHide');

            let total_price = $('.order__order__m-total_price span').text();

            if (total_price) {
                $('input[name="item_price"]').val(total_price);
            } else {
                let totalPrice = $('.order__order__m-total_price')
                    .text()
                    .trim()
                    .replace('$', '');
                $('input[name="item_price"]').val(totalPrice);
            }
        }

        if (!checkAutocomplete) {
            shippingPrice && (shippingPrice.innerHTML = '-');

            safeStyle(nextStage, el => el.style.opacity = 0.5);
            safeStyle(shipping, el => el.style.opacity = 0.5);

            total && (total.style.display = 'none');
            totalDefault && (totalDefault.style.display = 'block');

            orderTax && (orderTax.style.display = 'none');
            orderTaxDefault && (orderTaxDefault.style.display = 'flex');

            btnOrder && (btnOrder.style.display = 'none');
            btnOrderFake && (btnOrderFake.style.display = 'flex');

            firstStep?.classList.remove('step-done');
            nextStage?.classList.remove('step-done');

            $('#coupon').addClass('btnCouponHide');
        }

    }, 50);


    $('.checkout input,.checkout select').on('change', function () {
        let inputs = $('.checkout_form input:not(#apartment),select');
        let check_response = 1;
        for (let i = 0; i < inputs.length; i++) {
            if (!$(inputs[i]).val()) {
                $(inputs[i]).css('border-color', 'red');
                check_response = 0;
            } else {
                $(inputs[i]).css('border-color', '#000');
            }
        }

        if (check_response) {
            nextStage.style.opacity = 1;
            shipping.style.opacity = 1;
            firstStep.classList.add('step-done');

            allRadio.forEach((radio) => {
                radio.removeAttribute("disabled", "disabled");
            });

            orderTaxDefault.style.display = 'none';
            orderTax.style.display = 'block';
            totalDefault.style.display = 'none';
            total.style.display = 'block';
            shippingPrice.innerHTML = '$ 0.00';
        }

        if (check_response === 0) {
            shippingPrice.innerHTML = '-';
            /*taxPrice.innerHTML = '-';*/
            /*totalPrice.innerHTML = '-';*/
            nextStage.style.opacity = 0.5;
            shipping.style.opacity = 0.5;
            total.style.display = 'none';
            totalDefault.style.display = 'block';
            orderTax.style.display = 'none';
            /*orderTaxDefault.style.display = 'flex';*/
            btnOrder.style.display = 'none';
            btnOrderFake.style.display = 'flex';
            firstStep.classList.remove('step-done');

            allRadio.forEach((radio) => {
                radio.setAttribute("disabled", "disabled");
            });
        }

        $('input[name="cancel_url"]').val('https://barvinart.com/checkout/');


    });


    let infoBlock = document.querySelector('.checkoutOrderLoading');
    if (infoBlock) {
        infoBlock.style.display = 'none'
    }

    $('#place_order').on('click', function () {
        let first_name = $('#checkout_first_name').val();
        let last_name = $('#checkout_last_name').val();
        let country = $('#checkout_country').val();
        let street = $('#checkout_street').val();
        let apartment = $('#checkout_apartment').val();
        let city = $('#checkout_city').val();
        let postcode = $('#checkout_postcode').val();
        let phone = $('#checkout_phone').val();
        let email = $('#checkout_email').val();
        let notes = $('#checkout_notes').val();

        let infoBlock = document.querySelector('.checkoutOrderLoading');
        infoBlock.style.display = 'flex';


        let books = $('.order__union[data-pages]');
        let books_arr = [];
        let hasPrintBook = false;
        for (let j = 0; j < books.length; j++) {
            let book_type = $(books[j]).attr('data-type-book')
            if (book_type == 'Print book') {
                hasPrintBook = true;
            }
            if ($(books[j]).attr('data-page-cover') != '' && $(books[j]).attr('data-page-url') != '') {
                if (book_type != 'PDF book') {
                    books_arr.push({
                        "external_id": "item-reference-1",
                        "printable_normalization": {
                            "cover": {
                                "source_url": $(books[j]).attr('data-page-cover')
                            },
                            "interior": {
                                "source_url": $(books[j]).attr('data-page-url'),
                            },
                            "pod_package_id": "0850X0850FCPRESS080CW444MXX"
                        },
                        "quantity": $(books[j]).find('.box__qty span').text(),
                        "title": $(books[j]).find('.box__title').text()
                    });
                }
            } else {
                $('.notification-error').text('Something wrong');
                $('.notification-error').addClass('active');
                setTimeout(function () {
                    $('.notification-error').addClass('hide');
                }, 2000);

                setTimeout(function () {
                    $('.notification-error').removeClass('active');
                    $('.notification-error').removeClass('hide');
                }, 3000);

                return;
            }
        }
        let level = $('input[name="type_shipping"]:checked').attr('id').toUpperCase();
        let lulu_obj = {
            "contact_email": email,
            "external_id": first_name + ' ' + last_name + ' Books',
            "line_items": books_arr,
            "production_delay": 480,
            "shipping_address": {
                "city": city,
                "country_code": country,
                "name": first_name + ' ' + last_name,
                "phone_number": phone,
                "postcode": postcode,
                "state_code": apartment,
                "street1": street
            },
            "shipping_level": level,
        };

        shippings.forEach((btn) => {
            $(btn).on('click', function () {
                btnOrder.style.display = 'flex';
                btnOrderFake.style.display = 'none';
                orderTaxDefault.style.display = 'none';
                orderTax.style.display = 'block';
                totalDefault.style.display = 'none';
                total.style.display = 'block';
            });
        });


        /*if (country && street && city && postcode && books && apartment && first_name && last_name && phone && email && $('.order__order__m-total_price').text() != 'Please enter valid shipping info') {
            if(hasPrintBook)
            {
                $.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    data: {
                        'action': 'create_lulu_order',
                        'lulu_obj': lulu_obj
                    },
                    type: 'POST',
                    success: function (response) {
                        response = JSON.parse(response);
                        if($('input[name="item_price"]').val() > 0)
                        {
                            setTimeout(function() {
                                $('.wpsc-session').submit();
                            }, 1000);
                        }
                        else
                        {
                            window.location.href = "/thank-you/?order_id="+response.id;
                        }
                    }
                });
            }else
            {
                if($('input[name="item_price"]').val() > 0)
                {
                    setTimeout(function() {
                        $('.wpsc-session').submit();
                    }, 1000);
                }
                else
                {
                    window.location.href = "/thank-you/?type=free"
                }
            }
        }
        else
        {
            if(!hasPrintBook)
            {
                if($('input[name="item_price"]').val() > 0)
                {
                    setTimeout(function() {
                        $('.wpsc-session').submit();
                    }, 1000);
                }
                else
                {
                    window.location.href = "/thank-you/?type=free"
                }
            }
        }*/
        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'clear_cart',
            },
            type: 'POST',
            success: function (response) {
                console.log('clear');
            }
        });

        let btn = document.querySelector('#place_order'),
            btn2 = document.querySelector('.btnOrderHide');

        $("#place_order").remove();
        btn2.style.display = 'flex';
    });

    $(".real-radio-btn").on("click", function () {
        const labels = document.querySelectorAll('.shipping label');
        labels.forEach((label) => {
            label.style.background = '#F9F8F4';
            label.style.border = '1px solid #C4C4C4';
            nextStage.classList.remove('step-done');
        });

        if ($(this).is(":checked")) {
            $(this).parent().css({'backgroundColor': '#F1F9F4', 'border': '1px solid #408D5C'});
            nextStage.classList.add('step-done');
        }
    });

    let check_inp = 1;

    $('input,select').not('#coupon_code').on('change', function () {
        let inputs = $('.checkout_form input:not(#apartment),select');
        let hasPrintBook = false;
        for (let i = 0; i < inputs.length; i++) {
            let check_response = 1;
            check_inp = 0;
            if (!$(inputs[i]).val()) {
                $(inputs[i]).css('border-color', 'red');
                check_response = 0;
            } else {
                $(inputs[i]).css('border-color', '#000');
            }

            let country = $('#checkout_country').val();
            let street = $('#checkout_street').val();
            let city = $('#checkout_city').val();
            let postcode = $('#checkout_postcode').val();
            let books = $('.order__union[data-pages]');
            let apartment = $('#checkout_apartment').val();
            let phone = $('#checkout_phone').val();
            let books_arr = [];
            var payment_title = "";
            for (let j = 0; j < books.length; j++) {
                let book_type = $(books[j]).attr('data-type-book')
                if (book_type == 'Print book') {
                    hasPrintBook = true;
                }
                books_arr.push({
                    "page_count": $(books[j]).attr('data-pages'),
                    "pod_package_id": "0850X0850FCPRESS080CW444MXX",
                    "quantity": $(books[j]).find('.box__qty span').text()
                });
                payment_title += $(books[j]).find('.box__title').text() + ' x' + $(books[j]).find('.box__qty span').text() + ';<br/> ';
            }
            check_inp = 1;
            let level = $('input[name="type_shipping"]:checked').attr('id') ? $('input[name="type_shipping"]:checked').attr('id').toUpperCase() : '';
            if (country && street && city && postcode && books && apartment) {
                var lulu_obj = {
                    "line_items": books_arr,
                    "shipping_address": {
                        "city": city,
                        "country_code": country,
                        "postcode": postcode,
                        "state_code": apartment,
                        "street1": street,
                        "phone_number": phone
                    },
                    "shipping_level": level
                }
            }
        }
        if (hasPrintBook) {
            $.ajax({
                url: ajaxData.ajaxUrl,
                data: {
                    'action': 'get_order_price',
                    'lulu_obj': lulu_obj,
                },
                type: 'POST',
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.shipping_cost) {
                        let subtotal_price = $('.order__subtotal_price').text().split('$ ')[1];

                        let subtotal_price1 = $('.order__shipping_price').text().split('$ ')[1];
                        let total_price = parseFloat(subtotal_price)

                        let ship_price = parseFloat(response.total_tax);
                        let new_tax = (total_price + parseFloat(subtotal_price1)) * (response.total_tax / response.total_cost_excl_tax);
                        let couponDiscount = 0;
                        if (typeof localStorage.getItem('couponDiscount') !== 'undefined' && localStorage.getItem('couponDiscount') !== null) {
                            couponDiscount = localStorage.getItem('couponDiscount');
                            if (couponDiscount.indexOf('%') !== -1) {
                                couponDiscount = parseFloat(couponDiscount.replace('%', ''));

                                couponDiscount = (total_price + new_tax) * couponDiscount / 100

                                /*couponDiscount -= subtotal_price1*/
                            } else {
                                couponDiscount = parseFloat(couponDiscount)
                            }

                            /*couponDiscount += new_tax*/
                        }
                        $('.order__coupon_price-info').text('- $ ' + couponDiscount.toFixed(2))
                        let totalDiscount = total_price + new_tax - couponDiscount
                        if (totalDiscount <= 0) {
                            totalDiscount = 0;
                        }
                        let total = totalDiscount + parseFloat(subtotal_price1);
                        if (total <= 0) {
                            total = 0.00;
                        }
                        $('.order__order__m-total_price').text('$ ' + total.toFixed(2));
                        $('.order__tax_price').text('$ ' + new_tax.toFixed(2));
                        $('input[name="item_price"]').val(total.toFixed(2));
                        $('input[name="item_name"]').val(payment_title);
                        $('#coupon').removeClass('btnCouponHide')

                    } else {
                        $('.order__order__m-total_price').text('Please enter valid shipping info');
                        $('.order__order__m-total_price').addClass("no-valid");
                    }
                }
            });
        } else {
            $('#coupon').removeClass('btnCouponHide')
        }
    });
    $('input[name="type_shipping"]').on('change', function () {
        $('.order__shipping_price').text($('input[name="type_shipping"]:checked').attr('data-price'));
    });
}(jQuery));
