jQuery(function() {
    fetch(drupalSettings.path.baseUrl + 'cart')
        .then(response => response.json())
        .then(data => displayItems(data.cart));
    
    jQuery('#block-websitecartblock .block__content').on('click', '.delete-button', function() {
        const button = jQuery(this);
        console.log(button.data('itemid'));
        clearItem(button.data('itemid'));
    });

    jQuery('.add-item-placeholder').each(function() {
        const placeholder = jQuery(this);
        const nid = placeholder.data('itemid');
        this.html('<input type="number" /> <button class="add-item-button">Add</button>');
    });

    jQuery('#block-websitecartblock').on('click', '#ready-order-button', function() {
        jQuery('#finalize-order').show('slow');
        jQuery(this).hide();
        jQuery('#submit-order-button').show();
    });

    jQuery('#block-websitecartblock').on('input', 'input', function() {
        const emailValue = jQuery('#order-email').val().trim() != '';
        const phoneValue = jQuery('#order-phone').val().trim() != '';
        jQuery('#submit-order-button').attr('disabled', !(emailValue || phoneValue));
    });
});

function addItem(nid) {
    const quantity = jQuery('#add-item-quantity-' + nid).val();
    fetch(drupalSettings.path.baseUrl + 'cart/add?id=' + nid + '&quantity=' + quantity)
        .then(response => response.json())
        .then(data => displayItems(data.cart));

}

function clearItem(id) {
    alert("clearing " + id);
    fetch(drupalSettings.path.baseUrl + 'cart/remove?id=' + id)
        .then(response => response.json())
        .then(data => displayItems(data.cart));
}

function displayItems(cart) {
    let html = `<table id="cart-items">
                <tbody>`;
    let total = 0;
    for (item of cart['items']) {
        const subtotal = (item.unit_price * item.quantity).toFixed(2);
        html += `<tr>
                    <td><button class="delete-button" data-itemid="${ item.item_id }">X</button></td>                    
                    <td>
                        <span class="item-title">${ item.title }</span><br />
                        <span class="item-details">${ item.unit_price } / ${ item.unit_size }</span>
                    </td>
                    <td class="quantity-col">${ item.quantity }</td>
                    <td class="subtotal-col">${ subtotal }</td>
                </tr>`;
        total += parseFloat(subtotal);
    }
    if (cart['items'].length === 0) {
        html += `<tr>
                    <td colspan="4">Nothing ordered yet!</td>
                </tr>`;
    }
    const totalText = total.toFixed(2);
    html += `</tbody>
             <tfoot>
                <tr>
                    <th colspan="3" id="total-col">Total</th><th>${ totalText }</th>
                </tr>
             </tfoot>`;
    html += '</table>';

    html += '<form action="submit-order" method="post">';
    html += `<div id="finalize-order">
                <input id="order-email" type="email" name="email" placeholder="Your email address" /><br />
                <strong>or</strong>
                <input id="order-phone" type="text" name="phone" placeholder="Your phone number" />
               <textarea id="order-notes" name="notes" placeholder="Special notes"></textarea>
            </div>`;

    const ready_enabled = cart['items'].length > 0 ? '' : 'disabled';
    html += '<button id="ready-order-button" type="button" ' + ready_enabled + '>I\'m ready to order</button>';
    html += '<button id="submit-order-button" type="submit">Submit order</button>';
    html += '</form>';

    
    jQuery('#block-websitecartblock .block__content').html(html);

}

function placeOrderIsEnabled(cart) {
    const cartFilled = cart['items'].length > 0;

    return cartFilled;
}

function displayTotalArea() {

}