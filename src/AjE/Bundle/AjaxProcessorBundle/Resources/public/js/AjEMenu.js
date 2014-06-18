/*
 * Для использования нужен AjaxEngine
 * 
 * По нажатию на пункт меню делает ajax-запрос на url, 
 * указанный в ссылке и выводит результат в указанный
 * контейнер.
 * 
 * В темплейте
 * ---------------------------------------------
 * <ul class="menu">
 *   <li><a href="http:\\url1">Home</a></li>
 *   <li><a href="http:\\url2">Contacts</a></li>
 *   <li><a href="http:\\url3">Gallery</a></li>
 * </ul>
 * 
 * <div id="container"></div>
 * 
 * <script src="AjEMenu.js"></script>
 * <script>$('.menu').AjEMenu('#container')</script>
 * ---------------------------------------------
 * В контроллере
 * ---------------------------------------------
 * $request = Request::createFromGlobals();
 * if ($request->isXmlHttpRequest()){
 *     $code = $request->request->get('code');
 *     switch ($code){
 *          case 'Home':
 *              return new Request('This is homepage');
 *
 *          case 'Contacts':
 *              return new Request('This is contacts');
 *          
 *          case 'Gallery':
 *              return new Request('This is gallery');
 *
 *          default: 
 *              return new Request('YOU SHALL NOT PASS');
 *      }
 * }
 * ---------------------------------------------
 * 
 */
(function( $ ){
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
    $('a',this).click(function(e){ 
        var clicked = $(e.target);
        e.preventDefault();
        params.current = clicked.html();
        $(target).ajaxEngine(codes, params);
        return;   
    });
};
})(jQuery);