<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/products-ui') }}">← Back to Products</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white"><h5>Shopping Cart</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-table-body"></tbody>
                        </table>
                    </div>
                </div>
                <button class="btn btn-outline-danger" onclick="clearCart()">Clear Cart</button>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18%)</span>
                            <span id="tax">₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Grand Total</strong>
                            <strong class="h4 text-primary" id="grand-total">₹0.00</strong>
                        </div>
                        <button class="btn btn-success w-100 btn-lg">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadCart();
        });

        function loadCart() {
            $.get('/api/cart/view', function(items) {
                let body = $('#cart-table-body');
                body.empty();
                let subtotal = 0;

                items.forEach(item => {
                    let total = item.price * item.quantity;
                    subtotal += total;
                    body.append(`
                        <tr>
                            <td>${item.product.name}</td>
                            <td>₹${item.price}</td>
                            <td>
                                <input type="number" value="${item.quantity}" class="form-control form-control-sm" style="width: 70px" onchange="updateQty(${item.id}, this.value)">
                            </td>
                            <td>₹${total.toFixed(2)}</td>
                            <td>
                                <button class="btn btn-link text-danger p-0" onclick="removeItem(${item.id})"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `);
                });

                if(items.length === 0) body.append('<tr><td colspan="5" class="text-center p-4">Your cart is empty</td></tr>');
                
                let tax = subtotal * 0.18;
                $('#subtotal').text('₹' + subtotal.toFixed(2));
                $('#tax').text('₹' + tax.toFixed(2));
                $('#grand-total').text('₹' + (subtotal + tax).toFixed(2));
            });
        }

        function updateQty(id, qty) {
            $.post('/api/cart/update', { cart_id: id, quantity: qty }, () => loadCart());
        }

        function removeItem(id) {
            $.post('/api/cart/remove', { cart_id: id }, () => loadCart());
        }

        function clearCart() {
            $.post('/api/cart/clear', () => loadCart());
        }
    </script>
</body>
</html>