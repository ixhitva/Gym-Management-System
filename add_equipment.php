<?php
// ...existing code...
?>
<div class="form-container">
    <h2 class="form-title">Add New Equipment</h2>
    <form action="process_add_equipment.php" method="POST">
        <div class="form-group">
            <label for="name">Equipment Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Equipment</button>
    </form>
</div>
