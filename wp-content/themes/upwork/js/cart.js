(function ($) {
    if (window.location.href.indexOf("/cart/") > -1) {
        $('select[name="type"]').change(function () {
            var selectedType = $(this).val();
            var parentDiv = $(this).closest('.table__union.cart_item');
            var dataBookValue = parentDiv.data('book');
            $.ajax({
                url: ajaxData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'handle_book_type',
                    selectedType: selectedType,
                    bookId: dataBookValue
                },
                success: function (response) {
                    location.reload();
                }
            });
        });
    }

    $('.quantity__plus').on('click', function () {
        let current_item = $(this).parents('.cart_item');
        let item_id = parseInt($(current_item).attr('data-book'));
        let item_quantity = parseInt($(current_item).find('.quantity__count').text()) + 1;
        let productPhoto = $(current_item).find('.product__photo');
        let book_type = $(productPhoto).find('select[name="type"]').val();
        let is_pdf = 0;
        if (book_type === 'pdf') {
            is_pdf = 1;
        }
        $(this).parents('.cart_item').find('.quantity__count').text(item_quantity);

        setTimeout(function () {
            if (item_quantity == $(current_item).find('.quantity__count').text()) {
                updateCart([item_id, item_quantity, is_pdf]);
            }
        }, 500);
    });

    $('.quantity__minus').on('click', function () {
        let current_item = $(this).parents('.cart_item');
        let item_id = parseInt($(current_item).attr('data-book'));
        let item_quantity = parseInt($(current_item).find('.quantity__count').text()) - 1;
        let productPhoto = $(current_item).find('.product__photo');
        let book_type = $(productPhoto).find('select[name="type"]').val();
        let is_pdf = 0;
        if (book_type === 'pdf') {
            is_pdf = 1;
        }
        if (item_quantity > 0) {
            $(this).parents('.cart_item').find('.quantity__count').text(item_quantity);
            setTimeout(function () {
                if (item_quantity == $(current_item).find('.quantity__count').text()) {
                    updateCart([item_id, item_quantity, is_pdf]);
                }
            }, 500);
        }
    });

    $('.table__delete').on('click', function () {
        let current_item = $(this).parents('.cart_item');
        let item_id = parseInt($(current_item).attr('data-book'));
        let item_quantity = 0;
        updateCart([item_id, item_quantity], false, true);
        $(current_item).remove();
    });
    var doc = new jsPDF();
    $('#addBook').on('click', function (e) {
        e.preventDefault();

        var item_id = $('#addBook').attr('data-book-id');
        updateCart([item_id, 1, 1], true);

        // $('.quiz-option').css('display', 'block');
        // let item_id = $(this).attr('data-book-id');
        /*updateCart([item_id, 1],true);*/
    });
    $('.quiz-option__action').on('click', function (e) {
        e.preventDefault();
        var selectedValue = $('input[name="option"]:checked').val();
        var item_id = $('#addBook').attr('data-book-id');
        let is_pdf = 0;
        if (selectedValue == 'digital') {
            is_pdf = 1;
        }
        updateCart([item_id, 1, is_pdf], true);
    })
    $('.quiz-option__close').on('click', function () {
        $('.quiz-option').css('display', 'none');
    })

    $('.edited__add').on('click', function (e) {
        e.preventDefault();
        let item_id = $(this).attr('data-book-id');
        let tableBody = $(this).closest('.table__body');
        let book_type = tableBody.find('select[name="type"]').val();
        let pdf = 0;
        if (book_type === 'pdf') {
            pdf = 1;
        }
        updateCart([item_id, 1, pdf]);
    });

    $('body').on('click', '.footer__button_up.active', function (e) {
        /*e.preventDefault();*/
        // doc.save('document.pdf');
    });

    function updateCart($item, preview = false, if_delete = false) {
        let finalItems;
        $.ajax({
            url: ajaxData.ajaxUrl,
            data: {
                'action': 'update_cart',
                'item': $item,
            },
            type: 'POST',
            success: function (response) {
                response = JSON.parse(response);
                if (response) {
                    for (let i = 0; i < response.length; i++) {
                        updatePrices(response[i][0]);
                    }
                }

                if (preview) {
                    window.location.replace('/cart');
                }
                if (!if_delete) {
                    $('.notification-successful').text('Added to you cart');
                    $('.notification-successful').addClass('active');
                } else {
                    $('.notification-successful').text('Deleted from your cart');
                    $('.notification-successful').addClass('active');
                }
                setTimeout(function () {
                    location.reload();
                }, 1000);

            }
        });
        return finalItems;
    }

    function updatePrices($item_id) {
        let item = $('.cart_item[data-book="' + $item_id + '"]');
        let price = parseFloat($(item).find('.table__price').text());
        let count = parseInt($(item).find('.quantity__count').text());
        let subtotal = price * count;
        $(item).find('.table__total').text(subtotal.toFixed(2));
        let summ = 0;
        $('.table__total').each(function () {
            summ += +$(this).text() || 0;
        });
        $('.order__subtotal_price,.order__order__m-total_price').text('$ ' + summ.toFixed(2));
        checkEmptyCard(summ)
    }

    function checkInitCart() {
        const summ = $('.order__order__m-total_price span').text()
        checkEmptyCard(parseFloat(summ))
    }

    checkInitCart()

    function checkEmptyCard(summ) {
        if (summ) {
            $('#proceed_to_checkout').removeClass('disabled')
        } else {
            $('#proceed_to_checkout').addClass('disabled')
        }
    }
}(jQuery));
