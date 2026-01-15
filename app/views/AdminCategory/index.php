<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <h1>Danh mục</h1>
    <?php foreach ($category as $items): ?>
        <tr>
            <td><?= htmlspecialchars($items['name']) ?></td>
        </tr>
    <?php endforeach; ?>
</body>

</html>