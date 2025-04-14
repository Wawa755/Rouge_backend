<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

<h1>Look</h1>
<?php
$query = <<<SQL
SELECT
    Look.id AS look_id,
    Look.Name AS look_name,
    Look.Description AS look_description,
    Look.Price,
    Difficulty.Name AS difficulty_name,
    GROUP_CONCAT(DISTINCT Tags.Name) AS tags,
    GROUP_CONCAT(DISTINCT Product.Name) AS products
FROM Look
LEFT JOIN Difficulty ON Look.Difficulty_id = Difficulty.id
LEFT JOIN Look_Tag ON Look.id = Look_Tag.Look_id
LEFT JOIN Tags ON Look_Tag.Tag_id = Tags.id
LEFT JOIN Look_Product ON Look.id = Look_Product.Look_id
LEFT JOIN Product ON Look_Product.Product_id = Product.id
GROUP BY Look.id
ORDER BY Look.id;
SQL;

foreach ($db->query($query) as $row): ?>
  <div style="margin-bottom: 2em;">
    <h2>Look: <?= htmlspecialchars($row['look_name']) ?></h2>
    <strong>Description:</strong> <?= htmlspecialchars($row['look_description']) ?><br>
    <strong>Difficulty:</strong> <?= htmlspecialchars($row['difficulty_name']) ?><br>
    <strong>Price:</strong> $<?= htmlspecialchars($row['Price']) ?><br>
    <strong>Tags:</strong> <?= htmlspecialchars($row['tags']) ?><br>
    <strong>Products:</strong> <?= htmlspecialchars($row['products']) ?><br>

    <?php
    $image_stmt = $db->prepare("SELECT Img_path FROM 'Look images' WHERE Look_id = :look_id ORDER BY Img_order");
    $image_stmt->bindValue(':look_id', $row['look_id']);
    $image_stmt->execute();
    $images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($images):
        echo "<strong>Images:</strong><br>";
        foreach ($images as $img):
            echo '<img src="images/' . htmlspecialchars($img['Img_path']) . '" style="max-width: 200px; margin: 5px;">';
        endforeach;
    endif;
    ?>
  </div>
<?php endforeach; ?>

<h1>Look Bought</h1>
<?php
$query = <<<SQL
SELECT
    Look_bought.id AS bought_id,
    Look.id AS look_id,
    Look.Name AS look_name,
    Look.Description AS look_description,
    Look.Price,
    Difficulty.Name AS difficulty_name,
    GROUP_CONCAT(DISTINCT Tags.Name) AS tags,
    GROUP_CONCAT(DISTINCT Product.Name) AS products,
    Look_bought.Date_ordered,
    Look_bought.Date_delivered,
    User.Username AS user_name,
    User.Email AS user_email,
    User.Phone_number AS user_phone,
    User.Payment_method AS user_payment
FROM Look_bought
LEFT JOIN Look ON Look_bought.Look_id = Look.id
LEFT JOIN Difficulty ON Look.Difficulty_id = Difficulty.id
LEFT JOIN Look_Tag ON Look.id = Look_Tag.Look_id
LEFT JOIN Tags ON Look_Tag.Tag_id = Tags.id
LEFT JOIN Look_Product ON Look.id = Look_Product.Look_id
LEFT JOIN Product ON Look_Product.Product_id = Product.id
LEFT JOIN User ON Look_bought.User_id = User.id
GROUP BY Look_bought.id
ORDER BY Look_bought.id;
SQL;

foreach ($db->query($query) as $row): ?>
  <div style="margin-bottom: 2em;">
    <h2>Bought Look: <?= htmlspecialchars($row['look_name']) ?></h2>
    <strong>Description:</strong> <?= htmlspecialchars($row['look_description']) ?><br>
    <strong>Difficulty:</strong> <?= htmlspecialchars($row['difficulty_name']) ?><br>
    <strong>Price:</strong> $<?= htmlspecialchars($row['Price']) ?><br>
    <strong>Tags:</strong> <?= htmlspecialchars($row['tags']) ?><br>
    <strong>Products:</strong> <?= htmlspecialchars($row['products']) ?><br>
    <strong>Date Ordered:</strong> <?= htmlspecialchars($row['Date_ordered']) ?><br>
    <strong>Date Delivered:</strong> <?= htmlspecialchars($row['Date_delivered']) ?><br>

    <h3>User Info:</h3>
    <strong>Username:</strong> <?= htmlspecialchars($row['user_name']) ?><br>
    <strong>Email:</strong> <?= htmlspecialchars($row['user_email']) ?><br>
    <strong>Phone:</strong> <?= htmlspecialchars($row['user_phone']) ?><br>
    <strong>Payment Method:</strong> <?= htmlspecialchars($row['user_payment']) ?><br>

    <?php
    $image_stmt = $db->prepare("SELECT Img_path FROM 'Look images' WHERE Look_id = :look_id ORDER BY Img_order");
    $image_stmt->bindValue(':look_id', $row['look_id']);
    $image_stmt->execute();
    $images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($images):
        echo "<strong>Images:</strong><br>";
        foreach ($images as $img):
            echo '<img src="images/' . htmlspecialchars($img['Img_path']) . '" style="max-width: 200px; margin: 5px;">';
        endforeach;
    endif;
    ?>
  </div>
<?php endforeach; ?>

<h1>Users and Their Purchased Looks</h1>
<?php
$user_query = "SELECT * FROM User ORDER BY Username";

foreach ($db->query($user_query) as $user):
    $user_id = $user['id'];
?>
  <div style="margin-bottom: 3em;">
    <h2>User: <?= htmlspecialchars($user['Username']) ?></h2>
    <strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?><br>
    <strong>Phone:</strong> <?= htmlspecialchars($user['Phone_number']) ?><br>
    <strong>Payment Method:</strong> <?= htmlspecialchars($user['Payment_method']) ?><br>
    <strong>Address:</strong> <?= htmlspecialchars($user['Address']) ?><br>

    <h3>Bought Looks:</h3>
    <?php
    $purchase_query = <<<SQL
    SELECT
        Look_bought.id AS bought_id,
        Look.id AS look_id,
        Look.Name AS look_name,
        Look.Description AS look_description,
        Look.Price,
        Difficulty.Name AS difficulty_name,
        GROUP_CONCAT(DISTINCT Tags.Name) AS tags,
        GROUP_CONCAT(DISTINCT Product.Name) AS products,
        Look_bought.Date_ordered,
        Look_bought.Date_delivered
    FROM Look_bought
    LEFT JOIN Look ON Look_bought.Look_id = Look.id
    LEFT JOIN Difficulty ON Look.Difficulty_id = Difficulty.id
    LEFT JOIN Look_Tag ON Look.id = Look_Tag.Look_id
    LEFT JOIN Tags ON Look_Tag.Tag_id = Tags.id
    LEFT JOIN Look_Product ON Look.id = Look_Product.Look_id
    LEFT JOIN Product ON Look_Product.Product_id = Product.id
    WHERE Look_bought.User_id = :user_id
    GROUP BY Look_bought.id
    ORDER BY Look_bought.id;
    SQL;

    $stmt = $db->prepare($purchase_query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();
    $looks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($looks) > 0):
        foreach ($looks as $row): ?>
          <div style="margin: 1em 0; padding-left: 1em; border-left: 2px solid #ccc;">
            <h4>Look: <?= htmlspecialchars($row['look_name']) ?></h4>
            <strong>Description:</strong> <?= htmlspecialchars($row['look_description']) ?><br>
            <strong>Difficulty:</strong> <?= htmlspecialchars($row['difficulty_name']) ?><br>
            <strong>Price:</strong> $<?= htmlspecialchars($row['Price']) ?><br>
            <strong>Tags:</strong> <?= htmlspecialchars($row['tags']) ?><br>
            <strong>Products:</strong> <?= htmlspecialchars($row['products']) ?><br>
            <strong>Ordered:</strong> <?= htmlspecialchars($row['Date_ordered']) ?><br>
            <strong>Delivered:</strong> <?= htmlspecialchars($row['Date_delivered']) ?><br>

            <?php
            $image_stmt = $db->prepare("SELECT Img_path FROM 'Look images' WHERE Look_id = :look_id ORDER BY Img_order");
            $image_stmt->bindValue(':look_id', $row['look_id']);
            $image_stmt->execute();
            $images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($images):
                echo "<strong>Images:</strong><br>";
                foreach ($images as $img):
                    echo '<img src="images/' . htmlspecialchars($img['Img_path']) . '" style="max-width: 200px; margin: 5px;">';
                endforeach;
            endif;
            ?>
          </div>
    <?php endforeach;
    else:
        echo "<p>This user has not bought any looks yet.</p>";
    endif;
    ?>
  </div>
<?php endforeach; ?>
