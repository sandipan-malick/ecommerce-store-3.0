<?php
require_once '../includes/header.php';

$page_title = "About Us";
?>

<div class="container py-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-lg-6 d-flex flex-column justify-content-center">
            <h1 class="display-4 fw-bold mb-4">About Our Store</h1>
            <p class="lead mb-4">Welcome to <?php echo SITE_NAME; ?>, your one-stop destination for quality products at competitive prices. We're dedicated to providing an exceptional shopping experience with a wide selection of products, secure transactions, and excellent customer service.</p>
            <div class="mb-4">
                <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-primary me-2">
                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>
        <div class="col-lg-6">
            <img src="<?php echo SITE_URL; ?>/assets/images/about/about-hero.jpg" alt="About Us" class="img-fluid rounded shadow-sm">
        </div>
    </div>
    
    <!-- Our Story Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title">Our Story</h2>
            <div class="section-divider"></div>
        </div>
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title mb-3">How We Started</h3>
                    <p class="card-text">Founded in 2023, <?php echo SITE_NAME; ?> began as a small online store with a vision to create a seamless shopping experience for customers worldwide. What started as a passion project quickly grew into a thriving e-commerce platform.</p>
                    <p class="card-text">Our founders recognized the need for an online marketplace that not only offered quality products but also prioritized customer satisfaction and user experience. With this vision in mind, they set out to build a platform that would revolutionize online shopping.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title mb-3">Our Mission</h3>
                    <p class="card-text">At <?php echo SITE_NAME; ?>, our mission is to provide customers with a convenient, secure, and enjoyable shopping experience. We strive to offer a diverse range of high-quality products at competitive prices while maintaining exceptional customer service.</p>
                    <p class="card-text">We believe in building lasting relationships with our customers based on trust, transparency, and reliability. Our goal is to continuously improve and innovate to meet the evolving needs of our customers and exceed their expectations.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Why Choose Us Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="section-divider"></div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-box-open fa-3x text-primary"></i>
                </div>
                <h4>Quality Products</h4>
                <p class="text-muted">We carefully select each product in our inventory to ensure the highest quality standards. Our team thoroughly vets all items before they become available on our platform.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-truck fa-3x text-primary"></i>
                </div>
                <h4>Fast Shipping</h4>
                <p class="text-muted">We partner with reliable shipping carriers to ensure your orders are delivered promptly and safely. Track your shipments in real-time through your account dashboard.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-headset fa-3x text-primary"></i>
                </div>
                <h4>Customer Support</h4>
                <p class="text-muted">Our dedicated customer service team is available to assist you with any questions or concerns. We're committed to resolving issues quickly and efficiently.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <h4>Secure Payments</h4>
                <p class="text-muted">Shop with confidence knowing that your payment information is protected by industry-standard encryption and security protocols.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-undo fa-3x text-primary"></i>
                </div>
                <h4>Easy Returns</h4>
                <p class="text-muted">We offer a hassle-free return policy to ensure your satisfaction. If you're not happy with your purchase, we make it easy to return or exchange items.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="fas fa-tag fa-3x text-primary"></i>
                </div>
                <h4>Competitive Prices</h4>
                <p class="text-muted">We work directly with manufacturers and suppliers to offer you the best prices without compromising on quality.</p>
            </div>
        </div>
    </div>
    
    <!-- Team Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title">Our Team</h2>
            <div class="section-divider"></div>
            <p class="lead mb-5">Meet the dedicated professionals behind <?php echo SITE_NAME; ?> who work tirelessly to bring you the best shopping experience.</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card team-card border-0 shadow-sm">
                <img src="<?php echo SITE_URL; ?>/assets/images/about/team-1.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body text-center">
                    <h5 class="card-title mb-1">John Doe</h5>
                    <p class="text-muted mb-3">Founder & CEO</p>
                    <p class="card-text">John brings over 15 years of experience in e-commerce and retail management. His vision and leadership drive our company's growth and innovation.</p>
                    <div class="social-links">
                        <a href="#" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card team-card border-0 shadow-sm">
                <img src="<?php echo SITE_URL; ?>/assets/images/about/team-2.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body text-center">
                    <h5 class="card-title mb-1">Jane Smith</h5>
                    <p class="text-muted mb-3">Chief Operations Officer</p>
                    <p class="card-text">Jane oversees our day-to-day operations, ensuring that every aspect of our business runs smoothly, from inventory management to order fulfillment.</p>
                    <div class="social-links">
                        <a href="#" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card team-card border-0 shadow-sm">
                <img src="<?php echo SITE_URL; ?>/assets/images/about/team-3.jpg" class="card-img-top" alt="Team Member">
                <div class="card-body text-center">
                    <h5 class="card-title mb-1">Michael Johnson</h5>
                    <p class="text-muted mb-3">Customer Experience Manager</p>
                    <p class="card-text">Michael leads our customer service team, ensuring that every customer interaction exceeds expectations and builds lasting relationships.</p>
                    <div class="social-links">
                        <a href="#" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Testimonials Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title">What Our Customers Say</h2>
            <div class="section-divider"></div>
        </div>
        <div class="col-12">
            <div class="owl-carousel testimonial-carousel">
                <div class="testimonial-item p-4">
                    <div class="testimonial-content">
                        <p class="mb-4">"I've been shopping with <?php echo SITE_NAME; ?> for over a year now, and I'm consistently impressed by their product quality and customer service. The website is easy to navigate, and my orders always arrive on time."</p>
                        <div class="d-flex align-items-center">
                            <div class="testimonial-avatar me-3">
                                <img src="<?php echo SITE_URL; ?>/assets/images/about/testimonial-1.jpg" alt="Customer" class="rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-0">Sarah Williams</h5>
                                <div class="text-warning mb-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-muted mb-0">Loyal Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item p-4">
                    <div class="testimonial-content">
                        <p class="mb-4">"The return process was incredibly smooth when I needed to exchange a product. The customer service team was helpful and responsive, making what could have been a frustrating experience very pleasant."</p>
                        <div class="d-flex align-items-center">
                            <div class="testimonial-avatar me-3">
                                <img src="<?php echo SITE_URL; ?>/assets/images/about/testimonial-2.jpg" alt="Customer" class="rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-0">Robert Chen</h5>
                                <div class="text-warning mb-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p class="text-muted mb-0">New Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item p-4">
                    <div class="testimonial-content">
                        <p class="mb-4">"I appreciate the wide selection of products available on <?php echo SITE_NAME; ?>. I can find everything I need in one place, and the prices are very competitive. The shipping is fast, and the packaging is always secure."</p>
                        <div class="d-flex align-items-center">
                            <div class="testimonial-avatar me-3">
                                <img src="<?php echo SITE_URL; ?>/assets/images/about/testimonial-3.jpg" alt="Customer" class="rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-0">Emily Rodriguez</h5>
                                <div class="text-warning mb-1">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p class="text-muted mb-0">Regular Shopper</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="cta-section text-center p-5 rounded shadow-sm">
                <h2 class="mb-3">Ready to Start Shopping?</h2>
                <p class="lead mb-4">Join thousands of satisfied customers who trust <?php echo SITE_NAME; ?> for their shopping needs.</p>
                <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-lg btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize testimonial carousel
    $('.testimonial-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        navText: [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ],
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            }
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>