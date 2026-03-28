<?php
add_action('wp_ajax_process_stripe_payment', 'process_stripe_payment');
add_action('wp_ajax_nopriv_process_stripe_payment', 'process_stripe_payment');

function process_stripe_payment()
{
    require_once(get_template_directory() . '/libraries/vendor/autoload.php');
    \Stripe\Stripe::setApiKey(get_field("secret_key", 'option'));

    try {
        check_ajax_referer('nonce', 'nonce');

        $payment_method_id = sanitize_text_field($_POST['payment_method_id']);
        $post_id = intval($_POST['post_id']);
        $quiz_slug = sanitize_text_field($_POST['quiz_slug']);
        $items_count = intval($_POST['items_count']);

        if (!$post_id || !$payment_method_id || $items_count <= 0) {
            throw new Exception("Invalid request data");
        }

        $price_per_item = 1.00;

        $term = get_term_by('slug', $quiz_slug, 'templates');

        if ($term && !is_wp_error($term)) {
            $acf_price = get_field('field_624362b562548', 'templates_' . $term->term_id);

            if (!empty($acf_price) && is_numeric($acf_price)) {
                $price_per_item = (float)$acf_price;
            }
        }

        $total_price = $items_count * $price_per_item;

        $amount_in_cents = (int)round($total_price * 100);

        if ($amount_in_cents < 50) {
            throw new Exception("Minimum amount is $0.50");
        }

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount_in_cents,
            'currency' => 'usd',
            'payment_method' => $payment_method_id,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'return_url' => get_site_url() . '/payment-success'
        ]);

        if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_source_action') {
            wp_send_json_success([
                'requires_action' => true,
                'payment_intent_client_secret' => $paymentIntent->client_secret
            ]);
        } else if ($paymentIntent->status === 'succeeded') {

            update_post_meta($post_id, 'is_coloring_paid', true);

            $codeNumber = sanitize_text_field($_POST['codeNumber']);
            if ($codeNumber) update_post_meta($post_id, 'order_code_number', $codeNumber);

            wp_send_json_success(['success' => true]);

        } else {
            throw new Exception("Payment failed or requires manual review.");
        }

    } catch (\Stripe\Exception\CardException $e) {
        wp_send_json_error(['message' => $e->getError()->message]);

    } catch (Exception $e) {
        $raw_error_message = $e->getMessage();

        $safe_error_message = preg_replace('/(sk_live_|sk_test_|rk_live_|rk_test_)[a-zA-Z0-9]+/', '$1***[СКРЫТО]***', $raw_error_message);

        if (function_exists('sendTelegramMessage')) {
            sendTelegramMessage('Payment Error: ' . $safe_error_message);
        }

        wp_send_json_error(['message' => $safe_error_message]);
    }
}