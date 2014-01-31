/*
 * 
 * $('document').ready(function(){ 
    $('#main').ajaxEngine({code1:'test'});
    $('#main').AjEPaginator({
                   AjEParams:{
                       codes:{code1:'home'},
                       params:{url:'http://localhost/techpulse.net/web/app_dev.php/blog'}
                   }
                        
                   });
});       
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */

$.fn.AjEPaginator = function(params){ 
    
    var defaultPagParams = {koef:'150',urlMask:'/'};
    params.AjEParams.params.display_method = 'add';
    $('body').append('<div id="ajaxEngine_page" style="display:none">2</div>');
    var pageContainer = $('#ajaxEngine_page');
    var pagParams = mixObjects(defaultPagParams,params.pagParams);
    var AjEParams = {params:{}};
    
    
    
    AjEParams = mixObjects(defaultAjEParams,params.AjEParams);
    
    var koef = pagParams.koef; 
    var target = this;
    var urlMask = pagParams.urlMask;
    var nextPage, currentPage;
    console.log(nextPage);
    
    
    var counter=1;
    var urlContainer = $('#AjEData').html()
    var AjEDataArray=[urlContainer,urlContainer];
    var currentUrl;
    var firstRun=true;
   
    
    
    $(window).scroll(function() { 
        if($(window).scrollTop() + $(window).height() >= ($(document).height()-koef) && !target.hasClass('loading')){ 
            
            

            currentPage = parseInt(pageContainer.html());    
            nextPage = currentPage; 
            
            if(counter%2===0){
                AjEDataArray[0] = getLastPartUrl($('#AjEData').html());
                var zeroUrl = $('#AjEData').html();
            } else {
                AjEDataArray[1] = getLastPartUrl($('#AjEData').html());
                var firstTurn=true;
                var firstUrl = $('#AjEData').html();
            }
            
            counter=counter+1;
            
            if(AjEDataArray[0] !== AjEDataArray[1]){ 
                if(firstTurn){
                    if(firstRun){nextPage = 2;}
                    else nextPage = 2;
                    firstRun=false;
                    currentUrl = getUrlWithoutPage(firstUrl);
                } else {
                    if(firstRun){nextPage = 2;}
                    else nextPage = 2;
                    firstRun=false;
                    currentUrl = getUrlWithoutPage(zeroUrl);
                }
                
                
            }
            
   
            AjEParams.params.url = currentUrl+urlMask+nextPage;   
            $('#main').ajaxEngine(AjEParams.codes,AjEParams.params);
            
            
            target.addClass('loading');
            
        } else if ($(window).scrollTop() + $(window).height() < ($(document).height()-koef) && target.hasClass('loading') && target.hasClass('loading')) {
            
            target.removeClass('loading');
            pageContainer.html(parseInt(nextPage+1));
 
        }
        
        
        function getLastPartUrl(str) {
            var str = str||'/undefined';
            var lastPartUrl = str.substring(str.lastIndexOf(urlMask), str.length);
            if(lastPartUrl.match(/\d+/)){
                lastPartUrl = str.substring(0,str.lastIndexOf(urlMask));
                lastPartUrl=lastPartUrl.substring(lastPartUrl.lastIndexOf(urlMask),lastPartUrl.length);    
            }
            if(lastPartUrl.substring(lastPartUrl.length-1,lastPartUrl.length)==='#'){
                lastPartUrl=lastPartUrl.substring(0,lastPartUrl.length-1);
            }
            
            return lastPartUrl;
        }
        
        
        function getUrlWithoutPage(str) {
            var url = str||'/undefined';
            var lastPart = url.substring(url.lastIndexOf(urlMask), url.length);
            if(lastPart.match(/\d+/)){
                url = str.substring(0, url.length - (url.length-url.lastIndexOf(urlMask)));
            } else {
                url = str;
            }
            
            if(url.substring(url.length-1,url.length)==='#'){
                url=url.substring(0,url.length-1);
            }
            
            return url;
        };
        
        
    }); 
    
    
}    