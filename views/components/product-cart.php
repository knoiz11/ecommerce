<div class="col-md-3 mb-4">
    <div class="card h-100 shadow-sm border border-secondary rounded">
        <a href="<?php echo BASE_URL; ?>views/product/product.php?id=<?php echo $product['id']; ?>">
            <img src="<?php echo BASE_URL . $product['image_url']; ?>" 
                 class="card-img-top mx-auto p-2" 
                 alt="Product Image" 
                 style="width: 100%; height: 200px; object-fit: contain;">
        </a>
        <div class="card-body text-center">
            <h5 class="card-title font-weight-bold"><?php echo $product['product_name']; ?></h5>
            <p class="card-text text-muted">Price: ₱<?php echo number_format($product['unit_price'], 2); ?></p>
            
            <form action="<?php echo BASE_URL; ?>app/cart/add_to_cart.php" method="POST">
                <input type="hidden" class="form-control" name="id" value="<?php echo $product['id']; ?>">
                <input type="hidden" class="form-control" name="quantity" value="1">
                <button href="#" class="btn btn-success btn-sm mt-2" <?php echo ($product['stocks'] <= 0 ? "disabled" : ""); ?>>
                    <?php echo ($product['stocks'] <= 0 ? "Sold out" : "Add to Cart"); ?>
                </button>
            </form>
        </div>
    </div>
</div>