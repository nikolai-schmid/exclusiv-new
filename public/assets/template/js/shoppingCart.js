var $ = jQuery;

window.onload = function() {
	var shoppingCart = new ShoppingCart	();
}

function ShoppingCart() {
	this.init();
}

ShoppingCart.prototype.init = function(){
	this.setListeners();
}

ShoppingCart.prototype.setListeners = function(){
	var it = this;
	
	var as = $("#shopping-cart-items").find("a");
	for (var i = 0; i < as.length; i++) {
		as[i].onclick = function(e) {
			it.executeLink(this.href);
			
			e.stopPropagation();
			return false;
		}
	}
	
	var bfButtons = $(".buy-form-container").find(".btn");
	for (var i = 0; i < bfButtons.length; i++) {
		bfButtons[i].onclick = function(e) {
			var form = $($(this).parent());
			
			$.ajax({
		           type: "POST",
		           url: form.attr("action"),
		           data: form.serialize(),
		           success: function(data)
		           {
		        	   it.refreshShoppingCart(data);
		           }
	         });
			
			e.stopPropagation();
			return false;
		};
	}
}

ShoppingCart.prototype.executeLink = function(href){
	var it = this;
	$.ajax({
		  url: href,
		  method: "POST",
		  type: "JSON",
		  success: function(data) {
			  it.refreshShoppingCart(data);
		  }
	});
}

ShoppingCart.prototype.refreshShoppingCart = function(html) {
	var shoppingCart = $("#shopping-cart");

	if (shoppingCart.length > 0) {
		shoppingCart.replaceWith(html);
	} else {
		$("#sidebar").prepend(html);
	}
	
	this.setListeners();
}


