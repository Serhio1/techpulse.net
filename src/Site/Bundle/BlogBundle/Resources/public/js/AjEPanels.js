/*
 * Создает в выбраном DOM элементе контейнер с панелями
 * и кнопками управления. Одна из панелей активна.
 * В активную панель будет осуществляться подгрузка 
 * ajax-контента. Пользователь может сам выбрать активную
 * панель, добавлять, удалять, сворачивать и разворачивать
 * панели. (В будущем - создавать вкладки внутри,
 * вернуться на предыдущую загруженную в панель страницу,
 * реализация drag&drop интерфейса).
 * Использовать вместе c jQuery, AjaxEngine, AjEMenu. 
 * ---------------------------------------------------
 * Пример использования:
 * 
 * ---------------------------------------------------
 * В темплейте
 * ---------------------------------------------------
 * <script src="jquery.js"></script>
 * <script src="ajaxEngine.js"></script>
 * <script src="AjEMenu.js"></script>
 * <script src="AjEPanels.js"></script>
 * 
 * <ul class="nav">  
 *   <li><a href="http...">Home</a></li>
 *   <li><a href="http...">Contacts</a></li>
 *   <li><a href="http...">Gallery</a></li>
 * </ul>
 * 
 * <main></main>
 * 
 * <script>
 * jQuery(function(){
 *    $('main').AjEPanels().init();
 *    $('.nav').AjEMenu('#AjEActive');
 * });
 * </script>
 * 
 * 
 * 
 * 
 * ---------------------------------------------------
 * В контроллере
 * ---------------------------------------------------
 * 
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
 * --------------------------------------------------
 * 
 * 
 * 
 * 
 */
jQuery(function(){
(function( $ ){

    $.fn.AjEPanels = function(){
        
        var target = this;
        
        return function(){
            
            var container = {
                html: '<div id="AjEPanelsContainer"'
                     +' style="width: 100%;'
                     +'height: 100%;">'
                     +'<div class="addPanel">addPanel'
                     +'</div></div>',
                jQ: function(){
                        return $('#AjEPanelsContainer');
                    }
            };
            
            var panel = {
                
                html: function(num){
                    return '<div class="panel">'
                     +'<ul class="AjEPanelNav">'
                     +'<li class="indicator'
                     +' AjEPanelIndicator'
                     +num+'"></li>'
                     +'<li class="AjEPanelRemove"></li>'
                     +'</ul><div class="AjEPanel'
                     +num+'">';},
                count: 0,
                offset: 0,
                jQ: function(){
                        return $('.panel');
                    }
                
            };
            
            
            
            panel.init = function()
            {
                target.append(container.html);
                container.jQ().append(this.html);
                var descr =$(this.jQ(),container.jQ),
                    bord = descr.outerWidth()
                        - descr.innerWidth(),
                    padd = descr.innerWidth()
                        - descr.width(),
                    marg = descr.outerWidth(true)
                        - descr.outerWidth();
                
                this.offset = parseInt(bord+padd+marg);
                descr.remove();
                
                this.initListeners();
                
                return this;
            };
            
            panel.initListeners = function()
            {
                $('.addPanel',container.jQ)
                .live('click', function(){
                    panel.add();
                });
                $('.indicator',panel.jQ)
                .live('click', function(){
                    panel.activate(this);
                });
                $('.AjEPanelRemove',panel.jQ)
                .live('click', function(){
                    panel.delete($(this).parent()
                    .parent());
                });
            };
            
            panel.add = function(i)
            {
                panel.count++;
                container.jQ().append(panel
                        .html(panel.count));
                
                if(i>1){panel.add(--i);}
                panel.activate();
                panel.autoFormat();
                
                return this;
            };
            
            panel.autoFormat = function()
            {
                var containerWidth = container.jQ()
                        .width(),
                    panelPixWidth = (containerWidth
                        *100/panel.count/100)
                        -panel.offset,
                    panelPercWidth = panelPixWidth
                        *(100/containerWidth);
                $('.panel',container.jQ())
                    .width(panelPercWidth+'%');
                
                return this;
            };
            
            panel.delete = function(descr)
            {
                panel.count--;
                descr.remove();
                panel.autoFormat();
                panel.activate();
                
                return this;
            };
            
            panel.activate = function(indicator)
            {
                indicator=indicator
                          ||$('li.indicator').last();
                var classArr = $(indicator,panel)
                     .attr('class').split(' '),
                    panelNum;
            
                for(var i=0; i<classArr.length; i++){
                    if (classArr[i]
                    .indexOf('AjEPanelIndicator')+1){
                        panelNum = classArr[i]
                                  .replace(/\D+/g,"");
                    }
                }

                $('#AjEActive').attr('id','');
                $('.AjEPanel'+panelNum)
                    .attr('id','AjEActive');
                $('.activated').removeClass('activated');
                $(indicator).addClass('activated');
                
                return this;
            };
            
            panel.resize = function()
            {
                alert('panel was resized');
                
                return this;
            };
            
            return panel;
        }();

    };

})(jQuery);
        
       $('main').AjEPanels().init().add();

});