<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <?php foreach ($products as $product): ?>
        <div>
            <h2><?= $product['name'] ?></h2>
            <p>Price: <?= $product['price'] ?></p>
        </div>
    <?php endforeach; ?>
</body>

</html>