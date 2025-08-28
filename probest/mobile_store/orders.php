<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$current_page = 'orders';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการการขาย (POS) - Mango Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .main-content { padding: 20px 40px !important; }
        #search-results {
            position: absolute; background: white; border: 1px solid #ccc;
            width: 100%; max-height: 200px; overflow-y: auto;
            z-index: 1000; list-style: none; padding: 0; margin: 0;
        }
        #search-results li { padding: 10px; cursor: pointer; }
        #search-results li:hover { background-color: #f0f0f0; }
    </style>
</head>
<body>
<div class="container-fluid p-0 d-flex">
    <?php include 'sidebar.php'; ?>
    <main class="main-content flex-grow-1">
        <?php include 'header.php'; ?>
        
        <h1 class="mb-4">สร้างรายการขายใหม่</h1>
        
        <form id="pos-form" action="process_order.php" method="POST">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="product-search" class="form-label">ค้นหาสินค้า (ชื่อ หรือ IMEI)</label>
                    <div class="position-relative">
                        <input type="text" id="product-search" class="form-control" placeholder="พิมพ์เพื่อค้นหา...">
                        <ul id="search-results"></ul>
                    </div>
                </div>
            </div>

            <h3 class="mt-4">รายการขาย</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th style="width: 150px;">ราคาต่อหน่วย</th>
                        <th style="width: 120px;">จำนวน</th>
                        <th style="width: 150px;">ราคารวม</th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody id="order-items">
                    </tbody>
            </table>

            <div class="d-flex justify-content-end">
                <h2>ยอดรวม: <span id="total-amount">0.00</span> ฿</h2>
            </div>
            
            <hr>
            
            <h3 class="mt-4">ข้อมูลลูกค้า (ถ้ามี)</h3>
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label class="form-label">เบอร์โทรศัพท์ลูกค้า</label>
                    <input type="text" name="customer_phone" class="form-control">
                </div>
                 <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อลูกค้า</label>
                    <input type="text" name="customer_name" class="form-control">
                </div>
            </div>

            <input type="hidden" name="items_json" id="items_json">
            <input type="hidden" name="total_amount" id="total_amount_input">
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-success btn-lg">บันทึกการขาย</button>
            </div>
        </form>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    const orderItemsTable = document.getElementById('order-items');
    const totalAmountSpan = document.getElementById('total-amount');
    const posForm = document.getElementById('pos-form');
    const itemsJsonInput = document.getElementById('items_json');
    const totalAmountInput = document.getElementById('total_amount_input');

    // --- 1. ฟังก์ชันค้นหาสินค้า ---
    searchInput.addEventListener('keyup', function() {
        const term = searchInput.value;
        if (term.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        fetch(`search_products.php?term=${term}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                data.forEach(product => {
                    const li = document.createElement('li');
                    li.textContent = product.label;
                    li.dataset.product = JSON.stringify(product);
                    searchResults.appendChild(li);
                });
            });
    });

    // --- 2. ฟังก์ชันเพิ่มสินค้าลงตะกร้า ---
    searchResults.addEventListener('click', function(e) {
        if (e.target.tagName === 'LI') {
            const product = JSON.parse(e.target.dataset.product);
            
            // เช็คว่ามีสินค้านี้ในตะกร้าแล้วหรือยัง
            const existingRow = orderItemsTable.querySelector(`tr[data-product-id="${product.id}"]`);
            if (existingRow) {
                const qtyInput = existingRow.querySelector('.quantity-input');
                qtyInput.value = parseInt(qtyInput.value) + 1;
            } else {
                addToCart(product);
            }
            
            updateTotal();
            searchInput.value = '';
            searchResults.innerHTML = '';
        }
    });
    
    function addToCart(product) {
        const row = document.createElement('tr');
        row.dataset.productId = product.id;
        row.dataset.price = product.selling_price;
        
        row.innerHTML = `
            <td>${product.name}</td>
            <td>${parseFloat(product.selling_price).toFixed(2)}</td>
            <td><input type="number" class="form-control quantity-input" value="1" min="1" max="${product.stock_quantity}"></td>
            <td class="subtotal">${parseFloat(product.selling_price).toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove-item">ลบ</button></td>
        `;
        orderItemsTable.appendChild(row);
    }

    // --- 3. ฟังก์ชันอัปเดตยอดรวม และจัดการ Event ในตะกร้า ---
    orderItemsTable.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            updateTotal();
        }
    });
    
    orderItemsTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    });

    function updateTotal() {
        let total = 0;
        orderItemsTable.querySelectorAll('tr').forEach(row => {
            const price = parseFloat(row.dataset.price);
            const quantity = parseInt(row.querySelector('.quantity-input').value);
            const subtotal = price * quantity;
            row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            total += subtotal;
        });
        totalAmountSpan.textContent = total.toFixed(2);
    }
    
    // --- 4. ฟังก์ชันก่อน Submit ฟอร์ม ---
    posForm.addEventListener('submit', function(e) {
        const items = [];
        orderItemsTable.querySelectorAll('tr').forEach(row => {
            items.push({
                id: row.dataset.productId,
                quantity: row.querySelector('.quantity-input').value,
                price: row.dataset.price
            });
        });

        if (items.length === 0) {
            alert('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ');
            e.preventDefault();
            return;
        }

        itemsJsonInput.value = JSON.stringify(items);
        totalAmountInput.value = parseFloat(totalAmountSpan.textContent).toFixed(2);
    });
});
</script>
</body>
</html>