function ajax_get(url,callback,type='JSON'){
  $.get(url,function(rep){
    if(rep.code == 401){
      window.location.href="/admin/";
    }
    else{
      callback(rep);
    }
  },type);
}

function ajax_post(url,data,callback,type='JSON'){
  $.post(url,data,function(rep){
    if(rep.code == 401){
      window.location.href="/admin/";
    }
    else{
      callback(rep);
    }
  },type);
}
