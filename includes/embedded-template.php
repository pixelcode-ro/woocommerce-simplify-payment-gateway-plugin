<?php

/**
 * Copyright (c) 2021 Mastercard
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/** @var WC_Order $order */
/** @var string $redirect_url */
/** @var bool $is_purchase */
/** @var string $public_key */
/** @var string[] $iframe_args */

$url_query       = parse_url($redirect_url, PHP_URL_QUERY);
$url_query_parts = $url_query ? explode('&', $url_query) : [];

?>

<script src="https://www.simplify.com/commerce/simplify.pay.js"></script>
<iframe name="embedded_pay" class="simplify-embedded-payment-form" style="width:100%; min-height:450px; margin:0; border:0; overflow:hidden;" <?php echo implode(' ', $iframe_args) ?>></iframe>

<form id="embedded-form" style="display:none;" action="<?php echo $redirect_url ?>" method="get">
	<?php foreach ($url_query_parts as $query_part) : ?>
		<?php
		$query = explode('=', $query_part);
		if (!isset($query[0], $query[1])) {
			continue;
		}
		?>
		<input type="text" name="<?php echo esc_attr($query[0]) ?>" value="<?php echo esc_attr($query[1]) ?>">
	<?php endforeach; ?>
	<input type="text" name="reference" value="">
	<input type="text" name="amount" value="">
	<?php if ($is_purchase) : ?>
		<input type="text" name="paymentId" value="">
		<input type="text" name="signature" value="">
		<input type="text" name="paymentDate" value="">
		<input type="text" name="paymentStatus" value="">
		<input type="text" name="authCode" value="">
	<?php else : ?>
		<input type="text" name="cardToken" value="">
	<?php endif; ?>
</form>

<script>
	var redirectUrl = "<?php echo $redirect_url ?>",
		isPurchase = <?php echo $is_purchase ? 'true' : 'false' ?>,
		publicKey = "<?php echo $public_key ?>",
		$embeddedForm = document.querySelector('#embedded-form');

	SimplifyCommerce.hostedPayments(
		function(data) {
			if (data.close && data.close === true) {
				return;
			}
			$embeddedForm.querySelector("[name=reference]").value = data.reference;
			$embeddedForm.querySelector("[name=amount]").value = data.amount;

			if (isPurchase) {
				$embeddedForm.querySelector("[name=paymentId]").value = data.paymentId;
				$embeddedForm.querySelector("[name=signature]").value = data.signature;
				$embeddedForm.querySelector("[name=paymentDate]").value = data.paymentDate;
				$embeddedForm.querySelector("[name=paymentStatus]").value = data.paymentStatus;
				$embeddedForm.querySelector("[name=authCode]").value = data.authCode;
			} else {
				$embeddedForm.querySelector("[name=cardToken]").value = data.cardToken;
			}
			$embeddedForm.submit();
		}, {
			scKey: publicKey
		}
	);
</script>

<?php /*
<a class="btn btn-width" href="<?php echo esc_url($order->get_cancel_order_url(wc_get_page_permalink('shop'))) ?>"><span><?php echo __('Renunță', 'woocommerce') ?></span></a>
*/ ?>
