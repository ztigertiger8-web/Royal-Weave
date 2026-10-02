//Login
function login() {
    var e = document.getElementById("em").value;

    if (e == "") {
        alert("please enter email");
    } 
    else if (e == "admin@mail.com") {
        window.location = "Admin.php";
    } 
    else {
        window.location = "MainFrame.php";
    }
}



//SignUp
function check() {
    var t1 = document.getElementById("p1").value;
    var t2 = document.getElementById("p2").value;
    var name = document.getElementById("nm").value;

    if (name == "") {
        alert("enter name");
        return false;
    }
    if (t1 != t2) {
        alert("not same");
        return false;
    }
    if (t1.length < 4) {
        alert("short");
        return false;
    }
    return true;
}



// Sign Up Validation Task 
function check() {
    var p = document.getElementById("p1").value;
    var m = document.getElementById("msg");
    
    m.innerHTML = ""; 

    if (p.length < 8) {
        m.innerHTML = "must be 8 characters";
        return false; 
    }

    window.location.href = "MainFrame.html"; 
    return false; 
}




//MainFrame
function save(x) {
    document.cookie = "last=" + x;
}
function show() {
    var s = document.cookie;
    if (s != "") {
        alert("last buy: " + s);
    }
}



//Search Validation
function doSearch() {
    var s = document.getElementById("searchInput").value;
    
    if (s == "") {
        alert("Please enter a product name");
        return false;
    }
    return true; 
}



//Cart Login Check 
function checkLoginBeforeCart() {
    var c = document.cookie;   
    if (c == "") {
        alert("Please login first to add items to cart!");
        window.location.href = "Login.html";
        return false;
    }   
    window.location.href = "Cart.html";
    return true;
}