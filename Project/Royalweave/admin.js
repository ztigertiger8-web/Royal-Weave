// admin.js
// JavaScript responsibility only:
// - Validate admin inputs before sending forms to PHP
// - Show user-friendly messages
// - Support product search/filtering in the admin table
// - Confirm update/delete actions
// Database connection, insert, update, delete, and select are handled by PHP/database team.

//----- ADMIN START -----
function adminStart() {
    var adminName = document.getElementById("adminName");

    if (adminName != null) {
        adminName.innerHTML = "Admin Panel";
    }

    setupAdminValidation();
}

//----- VALIDATION HELPERS -----
function isValidName(name) {
    name = name.trim();

    if (name.length < 3 || name.length > 50) {
        return false;
    }

    if (!/[a-zA-Z]/.test(name)) {
        return false;
    }

    return true;
}

function isValidDescription(desc) {
    desc = desc.trim();

    if (desc.length < 10 || desc.length > 250) {
        return false;
    }

    if (!/[a-zA-Z]/.test(desc)) {
        return false;
    }

    return true;
}

function isValidPrice(price) {
    price = price.trim();

    if (price == "" || isNaN(price)) {
        return false;
    }

    if (Number(price) <= 0 || Number(price) > 99999) {
        return false;
    }

    return true;
}

function isValidStock(stock) {
    stock = stock.trim();

    if (stock == "" || isNaN(stock)) {
        return false;
    }

    if (!Number.isInteger(Number(stock))) {
        return false;
    }

    if (Number(stock) < 0 || Number(stock) > 9999) {
        return false;
    }

    return true;
}

function isValidCategory(category) {
    category = String(category).trim();

    if (category == "" || category == "0" || category == "-1") {
        return false;
    }

    return true;
}

function isValidImage(image, required) {
    if (image == null) {
        return required == false;
    }

    var allowed = ["image/jpeg", "image/png", "image/jpg", "image/gif", "image/webp"];

    if (!allowed.includes(image.type)) {
        return false;
    }

    return true;
}

//----- MESSAGE HELPER -----
function showAdminMessage(message, isError) {
    var msg = document.getElementById("adminMsg");

    if (msg != null) {
        msg.innerHTML = message;

        if (isError) {
            msg.style.color = "red";
        } else {
            msg.style.color = "green";
        }
    } else {
        alert(message);
    }
}

//----- ADD PRODUCT VALIDATION -----
function validateAddProduct() {
    var name = getInputValue("productName");
    var desc = getInputValue("productDesc");
    var price = getInputValue("productPrice");
    var stock = getInputValue("productStock");

    var category = "";

    if (document.getElementById("productCategory") != null) {
        category = document.getElementById("productCategory").value;
    } else if (document.getElementById("category_id") != null) {
        category = document.getElementById("category_id").value;
    }

    var imageInput = document.getElementById("productImage");
    var image = null;

    if (imageInput != null && imageInput.files.length > 0) {
        image = imageInput.files[0];
    }

    if (!isValidName(name)) {
        showAdminMessage("Product name must be 3-50 characters and contain letters.", true);
        return false;
    }

    if (!isValidDescription(desc)) {
        showAdminMessage("Description must be 10-250 characters and contain letters.", true);
        return false;
    }

    if (!isValidCategory(category)) {
        showAdminMessage("Please select a valid category.", true);
        return false;
    }

    if (!isValidPrice(price)) {
        showAdminMessage("Price must be a positive number greater than 0.", true);
        return false;
    }

    if (!isValidStock(stock)) {
        showAdminMessage("Stock must be a whole number from 0 to 9999.", true);
        return false;
    }

    if (!isValidImage(image, true)) {
        showAdminMessage("Please choose a valid product image: JPG, PNG, GIF, or WEBP.", true);
        return false;
    }

    showAdminMessage("Product input is valid. Sending to PHP...", false);
    return true;
}

//----- UPDATE PRODUCT VALIDATION -----
function validateUpdateProduct(id) {
    var name = getInputValue("name_" + id);
    var desc = getInputValue("desc_" + id);
    var price = getInputValue("price_" + id);
    var stock = getInputValue("stock_" + id);

    var imageInput = document.getElementById("updateImage_" + id);
    var image = null;

    if (imageInput != null && imageInput.files.length > 0) {
        image = imageInput.files[0];
    }

    if (!isValidName(name)) {
        alert("Product name must be 3-50 characters and contain letters.");
        return false;
    }

    if (!isValidDescription(desc)) {
        alert("Description must be 10-250 characters and contain letters.");
        return false;
    }

    if (!isValidPrice(price)) {
        alert("Price must be a positive number greater than 0.");
        return false;
    }

    if (!isValidStock(stock)) {
        alert("Stock must be a whole number from 0 to 9999.");
        return false;
    }

    if (!isValidImage(image, false)) {
        alert("Choose a valid image file: JPG, PNG, GIF, or WEBP.");
        return false;
    }

    return confirm("Update this product?");
}

//----- REMOVE ENTIRLY CONFIRMATION -----
function confirmRemoveEntirelyProduct() {
    return confirm("RemoveEntirly will permanently delete this product from the database. Continue?");
}

//----- SEARCH / FILTER PRODUCTS -----
function searchProduct() {
    var searchInput = document.getElementById("adminSearch");

    if (searchInput == null) {
        return false;
    }

    var text = searchInput.value.toLowerCase().trim();
    var tableBody = document.getElementById("productTableBody");

    if (tableBody == null) {
        return false;
    }

    var rows = tableBody.getElementsByTagName("tr");

    for (var i = 0; i < rows.length; i++) {
        var rowText = rows[i].innerText.toLowerCase();

        if (rowText.includes(text)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }

    return false;
}

//----- SETUP FORM VALIDATION AUTOMATICALLY -----
function setupAdminValidation() {
    var addForm = document.getElementById("addProductForm");

    if (addForm != null) {
        addForm.onsubmit = function () {
            return validateAddProduct();
        };
    }

    var searchInput = document.getElementById("adminSearch");

    if (searchInput != null) {
        searchInput.onkeyup = function () {
            searchProduct();
        };
    }
}

//----- SMALL HELPER -----
function getInputValue(id) {
    var input = document.getElementById(id);

    if (input == null) {
        return "";
    }

    return input.value.trim();
}