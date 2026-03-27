(function ($) {
    $('#coupon').on('click',function ()
    {
        let couponCode = $('#coupon_code').val();
        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'set_coupon',
                'couponCode': couponCode,
            },
            type: 'POST',
            success: function (response) {
                if(response.status == 'success')
                {
                    let old_price = $('.order__order__m-total_price').text();

                    let shipp = $('input[name="type_shipping"]:checked').attr('data-price')
                    $('.order__shipping_price').text($('input[name="type_shipping"]:checked').attr('data-price'));
                    let shippPrice = shipp.replace('$ ', '');
                    let priceValue = old_price.replace('$ ', '');

                    let successfulBlock = document.querySelector('.notification-successful'),
                    promocodeBlock = document.querySelector('.order__promocode_box'),
                    btnClose = document.querySelector('.order__button_close'),
                    btnSend = document.querySelector('.checkout__order .order__promocode .order__button'),
                    inputPromocode = document.querySelector('.checkout__order .order__promocode input');
                    let discount = 0;
                    if(response.type == 'fixed')
                    {
                        localStorage.setItem('couponDiscount', response.discount);
                        discount = parseFloat(response.discount)
                    }
                    else
                    {
                        localStorage.setItem('couponDiscount', (response.discount_percent+'%'));
                        discount = (priceValue - shippPrice) * response.discount_percent/100;
                    }

                    let taxPrice = $('.order__tax_price-info').text();
                    let taxPriceFloat = taxPrice.replace('$ ', '');
                    let oldNettoPrice = parseFloat(priceValue) - parseFloat(taxPriceFloat) - shippPrice;
                    $('.order__coupon_price-info').text('- $ ' + discount.toFixed(2))
                    let total_sum = 0;
                    if(response.type == 'fixed')
                    {
                        total_sum = (priceValue - shippPrice) - discount
                        if(total_sum < 0)
                        {
                            total_sum = parseFloat(shippPrice);
                        }
                        else
                        {
                            total_sum += parseFloat(shippPrice)
                        }
                    }
                    else
                    {
                        total_sum = priceValue - discount
                        if(total_sum <= 0)
                        {
                            total_sum = 0;
                        }
                    }
                    $('.hide_block').removeAttr('style');
                    $('.order__order__m-total_price').text('$ ' + total_sum.toFixed(2));
                    $('<span>', {
                        text: response.message,
                    }).insertAfter('#coupon');
                    $('input[name="item_price"]').val(total_sum);

                    if(successfulBlock){
                        successfulBlock.innerHTML = 'Coupon has applied'
                        btnSend.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect width="24" height="24" rx="12" fill="white"/> <path d="M6 12.8L9.2 16L17.2 8" stroke="#408D5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg> Applied'
                        successfulBlock.classList.add('active')
                        promocodeBlock.classList.add('order__promocode_done')
                        btnClose.style.display = 'block'
                        btnSend.style.pointerEvents = 'none'
                        inputPromocode.setAttribute('disabled', 'disabled');

                        setTimeout(() => {
                            successfulBlock.classList.remove('active')
                        }, 3000);

                        btnClose.addEventListener('click',(e)=>{
                            e.preventDefault()
                            promocodeBlock.classList.remove('order__promocode_done')
                            btnSend.innerHTML = 'Apply'
                            btnSend.style.pointerEvents = ''
                            btnClose.style.display = 'none'
                            inputPromocode.removeAttribute('disabled');
                            let newShipp = $('input[name="type_shipping"]:checked').attr('data-price');
                            let newShippFloat = newShipp.replace('$ ','');
                            let newtaxPrice = $('.order__tax_price-info').text();
                            let newtaxPriceFloat = taxPrice.replace('$ ', '');
                            oldNettoPrice += parseFloat(newShippFloat) + parseFloat(newtaxPriceFloat)
                            $('.order__order__m-total_price').text('$ ' + oldNettoPrice);
                            $('input[name="item_price"]').val(oldNettoPrice);
                            $('.hide_block').css('display', 'none');
                            localStorage.removeItem('couponDiscount');
                        })
                    }
                }
                else
                {
                    let errorBlock = document.querySelector('.notification-error'); 

                    $('<span>', {
                        text: response.message,
                    }).insertAfter('#coupon');

                    if(errorBlock){
                        errorBlock.innerHTML = response.message
                        errorBlock.classList.add('active')

                        setTimeout(() => {
                            errorBlock.classList.remove('active')
                        }, 3000);
                    }
                }
            }
        });
    })
    $('.tab').on('click', function (e) {
        e.preventDefault();
        $('.active.tab').removeClass('active');
        $(this).addClass('active');
        let inputs = $('.checkout_form input:not(#apartment),select');
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
            let payment_title = "";
            for (let j = 0; j < books.length; j++) {
                books_arr.push({
                    "page_count": $(books[j]).attr('data-pages'),
                    "pod_package_id": "0850X0850FCPRESS080CW444MXX",
                    "quantity": $(books[j]).find('.box__qty span').text()
                });
                payment_title += $(books[j]).find('.box__title').text() + ' x' + $(books[j]).find('.box__qty span').text() + ';<br/> ';
            }
            check_inp = 1;
            let level = $('.tab.active').text().toUpperCase();
            if (country && street && city && postcode && books && apartment) {
                $('.loader').addClass('active');
                let lulu_obj = {
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

                $.ajax({
                    url: ajaxData.ajaxUrl,
                    data: {
                        'action': 'get_order_price',
                        'lulu_obj': lulu_obj,
                    },
                    type: 'POST',
                    success: function (response) {
                        response = JSON.parse(response);
                        $('.loader').removeClass('active');
                        if (response.shipping_cost) {
                            let subtotal_price = $('.order__subtotal_price').text().split('$ ')[1];
                            let total_price = parseFloat(subtotal_price) + parseFloat(response.shipping_cost.total_cost_incl_tax);
                            $('.order__order__m-total_price').text('$ ' + total_price);
                            let ship_price = parseFloat(response.shipping_cost.total_cost_incl_tax);
                            /*$('.order__shipping_price').text('$ ' + ship_price.toFixed(2));*/
                            $('input[name="item_price"]').val(total_price);
                            $('input[name="item_name"]').val(payment_title);
                            $('#coupon').removeClass('btnCouponHide')
                        } else {
                            $('.order__order__m-total_price').text('Eror request');
                        }
                    }
                });
            }
        }
    });
             $('#place_order').on('click', function (e) {
                 e.preventDefault();
                 localStorage.removeItem('couponDiscount');
                 let total = $('.order__order__m-total_price.order__order__m-total_price-info').text();
                 let tax = $('.order__tax_price.order__tax_price-info').text();
                 total = total.substring(2);
                 tax = tax.substring(2);
                 //total = parseFloat(total);
                 let itemslist = document.querySelectorAll('.box');
                 let items = [];

                 let country = $('#checkout_country').val();
                 let firstName = $('#checkout_first_name').val();
                 let lastName = $('#checkout_last_name').val();
                 let phone = $('#checkout_phone').val();
                 let email = $('#checkout_email').val();
                 let street = $('#checkout_street').val();
                 let city = $('#checkout_city').val();
                 let postcode = $('#checkout_postcode').val();
                 let apartment = $('#checkout_apartment').val();
                 let shipping_level = $('input[name="type_shipping"]:checked').attr('id').toUpperCase();
                 let coupon = 0;

                 if($('.order__coupon_price').length > 0)
                 {
                     coupon = $('.order__coupon_price').text();
                     coupon = coupon.replace('$ ', '');
                 }

                 itemslist.forEach(item => {
                     let book = {};
                     book.id = item.getAttribute('data-book-id');
                     book.title = item.querySelector('.box__title').textContent;
                     book.quontity = item.querySelector('.box__qty span').textContent;
                     book.price = item.getAttribute('data-book-total');
                     book.type = item.querySelector('#book_type').textContent;
                     items.push(book);
                 })

                 $.ajax({
                     url: ajaxData.ajaxUrl,
                     type: 'POST',
                     data: {
                         'action': 'create_order',
                         'total': total,
                         'tax': tax,
                         'books': Object.assign({}, items),
                         'country' : country,
                         'firstName' : firstName,
                         'lastName' : lastName,
                         'phone' : phone,
                         'email' : email,
                         'street' : street,
                         'city' : city,
                         'postcode' : postcode,
                         'apartment' : apartment,
                         'coupon' : coupon,
                         'shipping_level' : shipping_level,
                     },
                     success: function (response) {
                         response = JSON.parse(response);
                         $('input[name="success_url"]').val(function(index, currentValue) {
                             return currentValue + '&order_id=' + response;
                         });
                         if($('input[name="item_price"]').val() > 0)
                         {
                             $('.wpsc-session').submit();
                         }
                         else
                         {
                             window.location.href = "/thank-you/?order_id="+response + '&type=free';
                         }
                     },
                     error: function () {
                         console.log('error')
                     }
                 });

             });

}
    (jQuery)
);