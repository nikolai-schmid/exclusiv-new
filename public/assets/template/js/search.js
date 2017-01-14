var $ = jQuery;

$(window).load(function() {
	var searcher = new Searcher	();
});

function Searcher() {
	this.textSearchField = $("#textSearch");
	this.innerSearchField = $("#innerSearch");
	this.outerSearchField = $("#outerSearch");
	this.widthSearchField = $("#widthSearch");
	this.products = [];
	this.init();
}

Searcher.prototype.init = function() {
	var productElements = $(".product-definitions");
	
	for (var i = 0; i < productElements.length; i++) {
		var productElement = $(productElements[i]);
		var name = productElement.find("h2").text();
		var width = productElement.find(".width").text();
		var inner = productElement.find(".inner").text();
		var outer = productElement.find(".outer").text();
		var description = productElement.find(".description").text();
		
		this.products.push(new Product(name, description, inner, outer, width, productElement));
	}
	
	this.setListeners();
}

Searcher.prototype.setListeners = function() {
	var it = this;
	this.textSearchField.keyup(function () {
		it.search();
	});
	
	this.innerSearchField.keyup(function () {
		it.search();
	});
	
	this.outerSearchField.keyup(function () {
		it.search();
	});
	
	this.widthSearchField.keyup(function () {
		it.search();
	});
}

Searcher.prototype.search = function() {
	var foundProducts = this.searchProperties(this.textSearchField.val(), this.products, ['name', 'description']);
	var foundProducts = this.searchProperties(this.outerSearchField.val(), foundProducts, ['outer']);
	var foundProducts = this.searchProperties(this.innerSearchField.val(), foundProducts, ['inner']);
	var foundProducts = this.searchProperties(this.widthSearchField.val(), foundProducts, ['width']);

	for (var i = 0; i < this.products.length; i++) {
		if (foundProducts.indexOf(this.products[i]) > -1) {
			this.products[i].jqElement.parent().show();
		} else {
			this.products[i].jqElement.parent().hide();
		}
	}
}

Searcher.prototype.searchProperties = function(searchString, products, properties) {
	if (searchString.length == 0) {
		return products;
	}
	
	var foundProducts = [];
	
	for (var i = 0; i < products.length; i++) {
		var found = false;
		for (var x = 0; x < properties.length; x++) {
			if (products[i][properties[x]].toLowerCase().replace(/ /g,'').indexOf(searchString.toLowerCase().replace(/ /g,'')) > -1) {
				found = true;
			}
		}
		
		if (found) {
			foundProducts.push(products[i]);
		}
	}
	
	return foundProducts;
}

function Product(name, description, inner, outer, width, jqElement) {
	this.name = name;
	this.description = description;
	this.inner = inner;
	this.outer = outer;
	this.width = width;
	this.jqElement = jqElement;
}	

