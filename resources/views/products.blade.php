<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">My Store</a>
            <div>
                <button class="btn btn-success me-2" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Product
                </button>
                <a href="{{ url('/cart-ui') }}" class="btn btn-warning">
                    <i class="fas fa-shopping-cart"></i> View Cart
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Our Products</h2>
        <div class="row" id="product-list"></div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="product_id">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" step="0.01" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let productModal = new bootstrap.Modal(document.getElementById('productModal'));

        $(document).ready(function() {
            loadProducts();
        });

        function loadProducts() {
            $.get('/api/products', function(products) {
                let container = $('#product-list');
                container.empty();
                products.forEach(product => {
                    container.append(`
                        <div class="col-md-4 mb-4" id="product-card-${product.id}">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title font-weight-bold">${product.name}</h5>
                                        <div>
                                            <button class="btn btn-sm btn-outline-info me-1" onclick="openEditModal(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted small">${product.description || ''}</p>
                                    <h4 class="text-primary">₹${product.price}</h4>
                                    <button class="btn btn-primary w-100 mt-2" onclick="addToCart(${product.id})">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                });
            });
        }

        function openAddModal() {
            $('#modalTitle').text('Add New Product');
            $('#product_id').val('');
           $('#productForm').trigger('reset');
            productModal.show();
        }

        function openEditModal(product) {
            $('#modalTitle').text('Edit Product');
            $('#product_id').val(product.id);
            $('#name').val(product.name);
            $('#description').val(product.description);
            $('#price').val(product.price);
            productModal.show();
        }

        function saveProduct() {
            let id = $('#product_id').val();
            let url = id ? '/api/products/update' : '/api/products/add';
            let data = {
                name: $('#name').val(),
                description: $('#description').val(),
                price: $('#price').val()
            };

            if (id) data.product_id = id;

            $.post(url, data, function(res) {
                alert(res.message);
                productModal.hide();
                loadProducts();
            }).fail(function(err) {
                alert('Error: ' + err.responseJSON.message);
            });
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                $.post('/api/products/delete', { product_id: id }, function(res) {
                    alert(res.message);
                    loadProducts();
                });
            }
        }

        function addToCart(id) {
            $.post('/api/cart/add', { product_id: id }, function(res) {
                alert(res.message);
            }).fail(function() {
                alert('Error adding to cart');
            });
        }
    </script>
</body>
</html>