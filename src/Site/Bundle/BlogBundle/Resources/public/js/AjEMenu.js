$.fn.AjEMenu = function(target){
    var codes = {},
        params = {individual:{}},
        count;
    $('a',this).each(function(i){
        count = parseInt(i+1);
        codes['code'+count] = $(this).html();
        params.individual['code'+count] = {};
        params.individual['code'+count] = {url:$(this).attr('href')};
    }); 
    this.click(function(e){
        var clicked = $(e.target);
        e.preventDefault();
        params.current = clicked.html();
        target.ajaxEngine(codes, params);
    });
        
};