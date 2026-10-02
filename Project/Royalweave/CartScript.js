//Cart

//Update quantity
function updateQuantity(itemID){
    //Update the corresponding item from the cart in the database here


    showTotalPrice();
}

//Delete one element
function deleteItem(itemID) {
    //Delete the corresponding item from the cart in the database here

    location.reload();
}

//Delete all items
function deleteAllItems(){
    //Empty the cart in the database here


    location.reload();
}



//Subtotal
function showTotalPrice(){
    cartProducts = [
            {
                id: "P1001",
                name: "Japanese Semiramis",
                price: 810,
                quantity: 1,
                image: "images/Fabric/beige.jpg"
            }, 
            {
                id: "P1002",
                name: "Chinese Semiramis",
                price: 400,
                quantity: 1,
                image: "images/Fabric/beige.jpg"
            }
    ];  //Replace this list with the actual list of products in the cart

    var total = 0;
    cartProducts.forEach(cartProduct => {
        total += cartProduct.price * cartProduct.quantity;
    });

    document.getElementById("subtotal").textContent = `Subtotal: ${total} SAR`;
}


//Complete Purchase
function completePurchase(){
    cartProducts = [
            {
                id: "P1001",
                name: "Japanese Semiramis",
                price: 810,
                quantity: 1,
                image: "images/Fabric/beige.jpg"
            }, 
            {
                id: "P1002",
                name: "Chinese Semiramis",
                price: 400,
                quantity: 1,
                image: "images/Fabric/beige.jpg"
            }
    ];  //Replace this list with the actual list of products in the cart

    var isPurchaseValid = true;
    var alertString = "";

    cartProducts.forEach(cartProduct => {
        databaseProduct = {
                id: "P1001",
                name: "Japanese Semiramis",
                desc: "Luxury half-stiff fabric, 3.5m length.",
                price: 810,
                stock: 25,
                image: "images/Fabric/beige.jpg"
            }; //Replace this with the actual corresponding product from the db

            if (cartProduct.quantity > databaseProduct.stock) {
                isPurchaseValid = false;
                alertString += `The requested quantity of ${cartProduct.name} is not available\n`
            }
    });

    if (isPurchaseValid) {
        //Make the purchase and modify the database accordingly here

        window.location.href = "Success.html";
    }
    else
    {
        alert(alertString);
    }
}

showTotalPrice();