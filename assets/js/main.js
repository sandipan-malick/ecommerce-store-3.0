// Document Ready Function
$(document).ready(function() {
    // Initialize Owl Carousel for Hero Banner
    $('.hero-carousel').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            }
        },
        navText: [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ]
    });

    // Initialize Owl Carousel for Featured Products
    $('.featured-products-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            }
        },
        navText: [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ]
    });

    // Product Quantity Increment/Decrement
    $('.quantity-control .btn-minus').on('click', function(e) {
        e.preventDefault();
        var input = $(this).siblings('input');
        var value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
            updateCartItem(input);
        }
    });

    $('.quantity-control .btn-plus').on('click', function(e) {
        e.preventDefault();
        var input = $(this).siblings('input');
        var value = parseInt(input.val());
        input.val(value + 1);
        updateCartItem(input);
    });

    // Update cart when quantity changes
    $('.cart-quantity-input').on('change', function() {
        updateCartItem($(this));
    });

    // Function to update cart item
    function updateCartItem(input) {
        var productId = input.data('product-id');
        var quantity = input.val();
        
        $.ajax({
            url: 'pages/update_cart.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                // Refresh cart totals
                refreshCart();
            },
            error: function(xhr, status, error) {
                console.error('Error updating cart:', error);
            }
        });
    }

    // Function to refresh cart totals
    function refreshCart() {
        $.ajax({
            url: 'pages/get_cart_totals.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('.cart-subtotal').text(data.subtotal);
                $('.cart-total').text(data.total);
                $('.cart-count').text(data.count);
            },
            error: function(xhr, status, error) {
                console.error('Error refreshing cart:', error);
            }
        });
    }

    // Remove item from cart
    $('.remove-from-cart').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var cartItem = $(this).closest('.cart-item');
        
        $.ajax({
            url: 'pages/remove_from_cart.php',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                // Remove the item from DOM
                cartItem.fadeOut(300, function() {
                    $(this).remove();
                    // Refresh cart totals
                    refreshCart();
                    
                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        $('.cart-items').html('<div class="empty-state"><i class="fas fa-shopping-cart"></i><h3>Your cart is empty</h3><p>Looks like you haven\'t added any products to your cart yet.</p><a href="index.php" class="btn btn-primary">Continue Shopping</a></div>');
                        $('.cart-summary').hide();
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error removing item from cart:', error);
            }
        });
    });

    // Add to cart functionality
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var quantity = $('#product-quantity').val() || 1;
        
        $.ajax({
            url: 'pages/add_to_cart.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                // Show success message
                showAlert('Product added to cart successfully!', 'success');
                
                // Update cart count in navbar
                refreshCart();
            },
            error: function(xhr, status, error) {
                console.error('Error adding to cart:', error);
                showAlert('Failed to add product to cart. Please try again.', 'danger');
            }
        });
    });

    // Function to show alert messages
    function showAlert(message, type) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                        message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
        
        $('.alert-container').html(alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }

    // Real-time search functionality
    $('#search-input').on('keyup', function() {
        var query = $(this).val();
        
        if (query.length > 2) {
            $.ajax({
                url: 'pages/search_ajax.php',
                type: 'GET',
                data: {
                    q: query
                },
                success: function(response) {
                    $('#search-results').html(response).show();
                },
                error: function(xhr, status, error) {
                    console.error('Error searching products:', error);
                }
            });
        } else {
            $('#search-results').hide();
        }
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('#search-results').hide();
        }
    });

    // Filter products by price range
    $('#price-range').on('change', function() {
        var maxPrice = $(this).val();
        $('#price-value').text('₹' + maxPrice);
        
        // Update product display based on price filter
        $('.product-card').each(function() {
            var productPrice = parseFloat($(this).data('price'));
            
            if (productPrice <= maxPrice) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });

    // Sort products
    $('#sort-products').on('change', function() {
        var sortBy = $(this).val();
        var productContainer = $('.products-container');
        var products = productContainer.children('.col-md-4').get();
        
        products.sort(function(a, b) {
            var aVal, bVal;
            
            if (sortBy === 'price-low-high') {
                aVal = parseFloat($(a).find('.product-card').data('price'));
                bVal = parseFloat($(b).find('.product-card').data('price'));
                return aVal - bVal;
            } else if (sortBy === 'price-high-low') {
                aVal = parseFloat($(a).find('.product-card').data('price'));
                bVal = parseFloat($(b).find('.product-card').data('price'));
                return bVal - aVal;
            } else if (sortBy === 'name-a-z') {
                aVal = $(a).find('.card-title').text().toLowerCase();
                bVal = $(b).find('.card-title').text().toLowerCase();
                return aVal.localeCompare(bVal);
            } else if (sortBy === 'name-z-a') {
                aVal = $(a).find('.card-title').text().toLowerCase();
                bVal = $(b).find('.card-title').text().toLowerCase();
                return bVal.localeCompare(aVal);
            }
            
            return 0;
        });
        
        $.each(products, function(index, product) {
            productContainer.append(product);
        });
    });

    // Form validation
    $('.needs-validation').on('submit', function(event) {
        if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        $(this).addClass('was-validated');
    });
});