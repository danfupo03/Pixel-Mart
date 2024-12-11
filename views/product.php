<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';

$pid = isset($_GET['pid']) ? $_GET['pid'] : null;

if ($pid) {
  $stmt = $conn->prepare("SELECT * FROM products WHERE pid = ?");
  $stmt->bind_param("i", $pid);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  echo "Product not found.";
  exit;
}

$product = $result->fetch_assoc();

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $_POST['userId'];
  $productId = $_POST['productId'];
  $quantity = $_POST['quantity'];

  $conn->begin_transaction();

  try {
    $stmt = $conn->prepare('INSERT INTO shopping_cart (uid, pid, quantity) VALUES (?, ?, ?)');
    $stmt->bind_param('iii', $userId, $productId, $quantity);
    $stmt->execute();

    $conn->commit();

    header('Location: shoppingCart');
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    echo "Error adding to cart: " . $e->getMessage();
  }
  $conn->begin_transaction();
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'includes/head.php'; ?>

<body>
  <?php include 'includes/navbar.php'; ?>

  <section>
    <div class="container mt-5">
      <div class="product">
        <img src="assets/images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" />
        <div class="product-info">
          <h2 class="title is-2"><?= $product['name'] ?></h2>
          <h1 class="title is-4 is-primary">$<?= $product['price'] ?></h1>
          <p class="content">
            <?= $product['description'] ?>
          </p>
          <h1 class="title is-5"><strong>Features:</strong></h1>
          <p class="content">
            <?= $product['features'] ?>
          </p>
          <form action="" method="POST">
            <input type="hidden" name="userId" value="<?= $uid ?>">
            <input type="hidden" name="productId" value="<?= $product['pid'] ?>">
            <input type="number" name="quantity" value="1" min="1" class="input">
            <div class="mt-5 buttons-container">
              <button class="button is-secondary" type="submit">
                <i class="fa-solid fa-cart-arrow-down"></i>
                Add to Cart
              </button>
              <button class="button is-danger" onclick="window.history.back();">
                <i class="fa-solid fa-arrow-left"></i> Go Back
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <script src="assets/js/product.js"></script>
</body>

</html>