/**
 * @Contact us 
 */

$(document).ready(function (){
    $('#Contact').on('submit', function(){
        $.ajax({
            url : OVEconfig.BASEURL+'/contact/',
            type : 'POST',
            dataType : 'json',
            data : $( this ).serialize(),
            beforeSend: function() {
                $('.default-load').fadeIn();
            },
            success : function(data){
                if(data){
                    if(data.error){
                        showmsg("error-msg message", data.msg);
                    }else{
                        showmsg("sucess-msg message", data.msg);
                    }
                }
                
                $('.default-load').fadeOut();
            },
            error : function(xhr, errorType, errorMsg){console.log(errorMsg); $('.default-load').fadeOut();}
        });
        return false;
    });
})

function showmsg (msgclass, msg){
    $( "#msg" ).removeAttr( "style" );
    $( "#msg" ).removeClass("error-msg sucess-msg").addClass( msgclass );
    $( '#msg' ).html(msg);
    $( "#msg" ).fadeOut( 5000 );
    if(msgclass == 'sucess-msg message'){
        $( '#Contact' ).each(function(){
            this.reset();
        });
    }
}
