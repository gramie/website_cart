jQuery(function() {
    fetch('cart')
        .then(response => response.json())
        .then(data => console.log(data));
});

function addItem(id, qty) {

}

function clearItem(id) {

}

function displayItems() {

}