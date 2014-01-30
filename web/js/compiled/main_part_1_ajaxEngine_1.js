/**
 * ajaxEngine - скрипт для упрощения работы с технологией ajax. 
 * 
 * Простейший пример использования:
 * ======================================================
 * (в темплейте)
 * 
 * <div id="wrapper"></div>
 * <button id="button"></button>
 * 
 * <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
 * <script src="ajaxEngine.js"></script>
 * <script>
 * $(document).ready(function(){
 *     $('#button').click(function(){
 *         $('#wrapper').ajaxEngine({item1:'test'});
 *     });
 * });
 * </script>
 * 
 * -----------------------------------------------------
 * (в контроллере)
 * 
 * if ($request->isXmlHttpRequest()){
 *     $code = $request->request->get('code');
 *     if($code == 'test'){
 *         return new Response('It works!');
 *     }
 * }    
 * =======================================================
 * 
 * Возможности:
 * =======================================================
 * Выводит в указанный элемент контент из заданного файла.
 * Возможность вывести несколько представлений по очереди
 * есть кнопки переключения между темплейтами.
 * 
 * 
 * в темплейте вызывать так:
 * 
 * 
 *         $('#admin').ajaxEngine({         //список кодов
 *                      item1:'login',      
 *                      item2:'register',
 *                      item3:'ajaxpag',
 *                      item4: ...
 *                      },{
 *                      data:'this is ajax data', //данные, которые будут передаваться на сервер
 *                      
 *                      current:'register',      //какой код отправится первым (по умолчанию item1)
 *                      
 *                      url:{{path('homepage')}}, //на какой адрес будет отсылаться запрос (по умолчанию - текущий адрес)
 *                      
 *                      next_prev_btn:'true',      //используются ли кнопки Вперед/Назад (по умолчанию - false)
 *                      
 *                      loop:'true',           //начать с первого, если дошли до последнего
 *                      next_btn_label:'To the next', //подпись кнопки "вперед" (по умолчанию - Next)
 *                      prev_btn_label:'To the prev', //подпись кнопки "назад" (по умолчанию - Back)
 *                      style:'margin-left:30px'    //стиль для контейнера
 *                      next_prev_btn_mapping: 'inside', //кнопки навигации внутрь загрузившегося блока
 *                      
 *                      });


                
                
            
 * 
 */



var defaultAjEParams = {
                        data:'',
                        url:$(location).attr('href'),
                        next_prev_btn: 'false',
                        next_prev_btn_mapping: 'inside',
                        loop: 'false',
                        next_btn_label:'Next',
                        prev_btn_label:'Back',
                        display_method:'replace',
                        nav_btns:'false',
                        individual:{
                            code1:{}
                        }
                    };





$.fn.ajaxEngine = function(codes, params){
    if(!$('#AjEData').length){
        $('body').append('<div id="AjEData" style="display:none"></div>'); //------
    }
    

    
params = params||defaultAjEParams;
params.current = params.current||codes.code1;



var indParamsObj={};
var paramsObj=defaultAjEParams;
individualParamsIncluding(params.individual);
var target = this;
var mask = 'code';
var currentCodeVal = params.current;
var prevCodeVal, nextCodeVal;
var prevCode,currentCode,nextCode;
var codesStr='';
var codesCount=0;
var ajaxEngineContainer = 'ajaxEngine_container';
var navObj={};






for (var item in codes){
  if(codes[item]===currentCodeVal){
      currentCode = item;
      var currentNum = parseInt(currentCode.replace(mask,'')); 
  }
  prevCode = mask+String(currentNum-1);
  prevCodeVal = codes[prevCode];
  nextCode = mask+String(currentNum+1);
  nextCodeVal = codes[nextCode];
  
  
  codesCount+=1;
  navObj[codesCount] = codes[item];
} 


paramsObj = mixObjects(defaultAjEParams,params);

$('#AjEData').html(paramsObj.url); //------

if(indParamsObj[currentCode]){
    
    var currentIndParamsObj = indParamsObj[currentCode];
    
    /*for (var param in params){
        if(!paramsObj[param] || paramsObj[param]!==params[param]){
            if(param !== 'individual'){
                paramsObj[param]=params[param];
            }
        }
    }*/
    
    
    
    
    for (var indParam in currentIndParamsObj){
        if(!paramsObj[indParam]||paramsObj[indParam]!==currentIndParamsObj[indParam]){
            paramsObj[indParam] = currentIndParamsObj[indParam];
        }
    }
}



$('AjEData').html(paramsObj.url);

$.post(paramsObj.url,
    {
        code:currentCodeVal,
        ajaxData:paramsObj.data
    },
        function(html){

            if(paramsObj.display_method === 'replace'){
                target.html('<div class="'+ajaxEngineContainer+'_class" id="'+ajaxEngineContainer+'_'+currentCodeVal+'" style="'+paramsObj.style+'">'+html+'</div>');
            }
            if(paramsObj.display_method === 'add'){
                $('.'+ajaxEngineContainer+'_class #ajaxEngine_next_btn_'+prevCodeVal).filter(':first').remove();               
                target.append('<div class="'+ajaxEngineContainer+'_class" id="'+ajaxEngineContainer+'_'+currentCodeVal+'" style="'+paramsObj.style+'">'+html+'</div>');
            }
            
            
            
            



if (paramsObj.nav_btns === 'true'){

            function objToList(obj, depth) {
            depth=depth||1;
            var str = '<ul class="depth'+depth+'">';
            
            for (var i in obj){
                if(typeof(obj[i])==="object"){
                    str +='<li><a>'+objToList(obj[i],depth+1)+'</a></li>';
                } else {
                    if (obj[i]===currentCodeVal){
                        str += getNavLiStr(obj[i],i,'current');
                    } else {
                        str += getNavLiStr(obj[i],i);
                    }
                } 
            }
            str += '</ul>';
            
            function getNavLiStr(id, html, navLiClass) {
                var str ='';
                str = '<li id="'+id+'"';
                if (navLiClass) {
                    str+=' class="'+navLiClass+'"';
                } else {
                    
                    var navBtnObject = $.extend(true, {}, params);
                    navBtnObject.current=id;
                    var navBtnStr = serialize(navBtnObject);
                    
                    
                    str+=' onclick=\"$(\'#'+target.attr('id')+'\').ajaxEngine({'+codesStr+'},{'+navBtnStr+'})\"';
                }
                
                //onclick=\"$(\'#'+target.attr('id')+'\').ajaxEngine({'+codesStr+'},{'+nextBtnStr+'})\"
                
                
                
                
                str+=' ><a>' + html + '</a></li>';
            
                return str;
            }
            
            return str;
        }
        
        var navWidget = '<nav class="ajaxengine_nav">'+objToList(navObj)+'<nav>';
            
            $('#'+ajaxEngineContainer+'_'+currentCodeVal).append(navWidget);
            
}            
          
            
        if(paramsObj.next_prev_btn==='true'){
            
            if(!params.next_btn_label) {
                params.next_btn_label='Next';
            }
            if(!params.prev_btn_label) {
                params.prev_btn_label='Back';
            }
            
            //params.next_btn_label=params.next_btn_label;
            
        for (var item in codes){
            codesStr+=item+':\''+codes[item]+'\',';
        }      
        
        
        
        
                
        
        
        
        
        
        
        
        
        
        var nextBtnObject = $.extend(true, {}, params);
        nextBtnObject.current=nextCodeVal;
        var nextBtnStr = serialize(nextBtnObject);
        
        var prevBtnObject = $.extend(true, {}, params);
        prevBtnObject.current=prevCodeVal;
        var prevBtnStr = serialize(prevBtnObject);
            
            
            if(params.loop==='true'){
                if(!nextCodeVal){
                    nextCodeVal = codes.item1;
                }
                if(!prevCodeVal){
                    prevCodeVal=codes['item'+codesCount];
                }
            }
            
            
        
         
         
         

         
          
          
            var backBtn = '<a id=\"ajaxEngine_back_btn_'+currentCodeVal+'\" class=\"ajaxEngine_back_btn\" onclick=\"$(\'#'+target.attr('id')+'\').ajaxEngine({'+codesStr+'},{'+prevBtnStr+'})\">'+paramsObj.prev_btn_label+'</a>';
            
            
            var nextBtn = '<a id=\"ajaxEngine_next_btn_'+currentCodeVal+'\" class=\"ajaxEngine_next_btn\" onclick=\"$(\'#'+target.attr('id')+'\').ajaxEngine({'+codesStr+'},{'+nextBtnStr+'})\">'+paramsObj.next_btn_label+'</a>';
            
            
            
            
          
            if(params.nav_btns==='true'){
            $('#'+ajaxEngineContainer+'_'+currentCodeVal).append();
        }
        
        
        
        var btnContainer = btnContainer = $('#'+ajaxEngineContainer+'_'+currentCodeVal);
        
        if(paramsObj.next_prev_btn_mapping==='inside'){
            btnContainer = $('#'+ajaxEngineContainer+'_'+currentCodeVal).children().filter(':first');
        }
            
            
            
            if(prevCodeVal && params.display_method !== 'add'){
                btnContainer.append(backBtn);
            }
            if(nextCodeVal){
                btnContainer.append(nextBtn);   
            }
        }
            
            
            
        });

         
         
         
         
         
         
         
//------------------------------------------------------  
         function serialize(obj) {
                var str = '';
                for (i in obj){
                    if(typeof(obj[i])==="object"){
                        str += i+':{'+serialize(obj[i])+'},';
                    } else {
                        str += (i + ': \'' + obj[i] + '\', ');
                    }
                }
                return str;
            }
//------------------------------------------------------
//если есть индивидуальные параметры, то делаем их основными



           function individualParamsIncluding(obj,indDepth) {
                var str = '';
                indDepth=indDepth||1;
                for (i in obj){
                    if(typeof(obj[i])==="object"){
                        
                        indDepth=indDepth+1;
                        indParamsObj[i] = obj[i];
                        if(indDepth>1){
                            str += i+':{'+individualParamsIncluding(obj[i],indDepth)+'},';
                            
                        }
                        
                        
                    } else {
                        if(indDepth>1){
                            str += i+':{'+obj[i]+'},';
                            //obj[i] = 'trololo';
                            indParamsObj[i] = obj[i];
                        }
                    }
                }
                return obj;
            }

        
    
};
                            
/*
 * Если в объекте нет параметра - берем его из другого объекта
 */

function mixObjects(defaultObject,object){
    object = object||{};
    for (var param in defaultObject){
        if(!object[param]){
            object[param]=defaultObject[param];
        }
    }

    return object;
}