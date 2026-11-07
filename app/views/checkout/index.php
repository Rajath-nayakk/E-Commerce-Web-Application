<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="/cart" class="text-decoration-none">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <!-- Checkout Steps Indicator -->
    <div class="row justify-content-center mb-4 pb-2">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center position-relative text-center small fw-bold">
                <div class="d-flex flex-column align-items-center">
                    <span class="badge rounded-circle bg-success text-white p-2 mb-1" style="width: 32px; height: 32px;"><i class="bi bi-check-lg"></i></span>
                    <span class="text-muted">1. Cart</span>
                </div>
                <div class="flex-grow-1 border-top border-2 border-primary mx-2" style="margin-top: -16px;"></div>
                <div class="d-flex flex-column align-items-center">
                    <span class="badge rounded-circle bg-primary text-white p-2 mb-1" style="width: 32px; height: 32px;">2</span>
                    <span class="text-primary">2. Delivery & Payment</span>
                </div>
                <div class="flex-grow-1 border-top border-2 border-muted mx-2" style="margin-top: -16px;"></div>
                <div class="d-flex flex-column align-items-center">
                    <span class="badge rounded-circle bg-secondary text-white p-2 mb-1" style="width: 32px; height: 32px;">3</span>
                    <span class="text-muted">3. Confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <form action="/checkout/process" method="POST" id="checkoutForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Left Column: Shipping & Payment Details -->
            <div class="col-lg-7">
                <!-- Shipping Address Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">1</span>
                        <h5 class="fw-bold m-0">Pan-India Delivery Address</h5>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_name" class="form-label small fw-semibold text-muted">Recipient Full Name</label>
                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="<?= e(old('shipping_name', $user['name'] ?? '')) ?>" placeholder="e.g. Jane Customer" required>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_phone" class="form-label small fw-semibold text-muted">Contact Mobile Number (for Delivery SMS & OTP)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-semibold">+91</span>
                            <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone" value="<?= e(old('shipping_phone', '+91 98765 43210')) ?>" placeholder="98765 43210" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="shipping_address" class="form-label small fw-semibold text-muted">Complete Delivery Address & 6-Digit PIN Code</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" placeholder="Flat 402, HighTech Residency, 100 Feet Road, Indiranagar, Bengaluru, Karnataka - 560038" required><?= e(old('shipping_address', 'Flat 402, HighTech Residency, 100 Feet Road, Indiranagar, Bengaluru, Karnataka - 560038')) ?></textarea>
                        <div class="form-text small text-muted">Include Flat/Door No, Street/Road, Landmark, City, State, and 6-digit PIN code.</div>
                    </div>
                </div>

                <!-- Payment Method Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">2</span>
                        <h5 class="fw-bold m-0">Payment Method</h5>
                    </div>

                    <!-- Radio Option 1: Cash on Delivery -->
                    <div class="form-check p-3 rounded-3 border mb-3 payment-radio-box active-choice" id="box-cod" onclick="selectRadio('pay_cod')">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_cod" value="cod" checked onchange="togglePaymentBox('cod')">
                        <label class="form-check-label w-100" for="pay_cod">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark d-block">Cash on Delivery (COD)</span>
                                    <span class="small text-muted">Pay via Cash, UPI QR, or Debit Card at your doorstep upon physical delivery.</span>
                                </div>
                                <i class="bi bi-cash-stack fs-3 text-success"></i>
                            </div>
                        </label>
                    </div>

                    <!-- Radio Option 2: Mock Card / UPI Payment -->
                    <div class="form-check p-3 rounded-3 border payment-radio-box" id="box-card" onclick="selectRadio('pay_card')">
                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_card" value="card" onchange="togglePaymentBox('card')">
                        <label class="form-check-label w-100" for="pay_card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-dark d-block">Online Card / UPI Gateway (Mock Simulation)</span>
                                    <span class="small text-muted">Simulate instant Indian payment authorization with test credentials.</span>
                                </div>
                                <div class="d-flex gap-2 fs-4 text-primary">
                                    <i class="bi bi-credit-card"></i>
                                    <i class="bi bi-qr-code"></i>
                                </div>
                            </div>
                        </label>

                        <!-- Collapsible Mock Card Fields -->
                        <div id="cardFields" class="mt-3 pt-3 border-top d-none">
                            <div class="alert alert-info py-2 small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                <strong>Viva Evaluation Simulator:</strong> Any card digits simulate <strong>Success</strong>. A card number ending in <code>0000</code> simulates a <strong>Declined Payment</strong>.
                            </div>

                            <div class="mb-3">
                                <label for="card_number" class="form-label small fw-semibold text-muted">Card Number (Visa / MasterCard / RuPay)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-credit-card-2-front"></i></span>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="4242 •••• •••• 4242" maxlength="19">
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-7">
                                    <label for="card_expiry" class="form-label small fw-semibold text-muted">Expiry (MM/YY)</label>
                                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="12/28" maxlength="5">
                                </div>
                                <div class="col-5">
                                    <label for="card_cvv" class="form-label small fw-semibold text-muted">CVV</label>
                                    <input type="password" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary Review -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 85px;">
                    <h5 class="fw-bold mb-3">Order Summary</h5>

                    <!-- Itemized List Snippet -->
                    <div class="list-group list-group-flush mb-3 overflow-auto" style="max-height: 260px;">
                        <?php foreach ($items as $item): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= upload_url($item['image']) ?>" alt="" class="rounded bg-light" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div>
                                        <div class="small fw-semibold text-truncate" style="max-width: 180px;"><?= e($item['name']) ?></div>
                                        <span class="text-muted small">Qty: <?= $item['quantity'] ?></span>
                                    </div>
                                </div>
                                <span class="small fw-bold text-dark"><?= format_price(((float)$item['price']) * ((int)$item['quantity'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Items Subtotal:</span>
                            <span class="fw-semibold text-dark"><?= format_price($totals['subtotal']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Pan-India Express Delivery:</span>
                            <?php if ($totals['shipping'] == 0): ?>
                                <span class="fw-bold text-success">FREE</span>
                            <?php else: ?>
                                <span class="fw-semibold text-dark"><?= format_price($totals['shipping']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php 
                        $gstIncluded = round(($totals['subtotal'] * 18) / 118, 2);
                        ?>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">GST Included (18%):</span>
                            <span class="text-muted"><?= format_price($gstIncluded) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 pt-2 border-top">
                            <span class="fs-5 fw-bold text-dark">Total Amount:</span>
                            <span class="fs-4 fw-extrabold text-primary"><?= format_price($totals['total']) ?></span>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold rounded-3 shadow-md">
                                <i class="bi bi-shield-check me-2"></i>Place Order Now
                            </button>
                        </div>

                        <div class="text-center small text-muted">
                            <i class="bi bi-lock-fill text-success me-1"></i>
                            Transactional stock deduction & atomic reservation guaranteed.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function selectRadio(id) {
    const radio = document.getElementById(id);
    if (radio) {
        radio.checked = true;
        togglePaymentBox(radio.value);
    }
}

function togglePaymentBox(type) {
    const cardFields = document.getElementById('cardFields');
    const boxCod = document.getElementById('box-cod');
    const boxCard = document.getElementById('box-card');

    if (type === 'card') {
        cardFields.classList.remove('d-none');
        boxCard.classList.add('active-choice');
        boxCod.classList.remove('active-choice');
    } else {
        cardFields.classList.add('d-none');
        boxCod.classList.add('active-choice');
        boxCard.classList.remove('active-choice');
    }
}
</script>
