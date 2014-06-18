$(document).ready(function(){
    function Slider(){
        var elWrap = $('.carousel_wrap');
	var visual = $('.visual_block');
	var carousel = visual.children('ul');
	var visible = 1;
	itemWidth = carousel.children().outerWidth(),
	itemsTotal = carousel.children().length,
	autoChange = 5000,
	btnNext = $('.next'),
	btnPrev = $('.prev');

	visual.css({'width': visible * itemWidth + 'px'});
	
	carousel.css({'width': itemsTotal * itemWidth,	'left': 0});
	
	function chengeLeft () {
		var item = carousel.children().eq(0);

		carousel.animate({left: -itemWidth}, 500, function() {
			item.clone().appendTo(carousel);		
			item.remove();
			carousel.css({"left": 0 });		
		});
	}	
	
	function chengeRigth () {
		var item = $(carousel).children().eq(-1);
		item.clone().prependTo(carousel);
		carousel.css({"left": -itemWidth});
		carousel.animate({left: 0}, 500);
		item.remove();	
	}	
	
	var interval = setInterval(chengeLeft, autoChange);

	btnNext.click(chengeLeft);
	
	btnPrev.click(chengeRigth);	
	
	elWrap.mouseover(function() {
		clearInterval(interval);
	});
	
	elWrap.mouseout(function() {
		interval = setInterval(chengeLeft, autoChange);
	});
        }
        
        Slider();
});
    
	




