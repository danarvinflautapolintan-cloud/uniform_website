document.addEventListener('click',function(e){if(e.target.closest('[data-print]'))window.print();});
document.addEventListener('submit',function(e){var m=e.target.getAttribute('data-confirm');if(m&&!confirm(m))e.preventDefault();});
document.addEventListener('change',function(e){
  var f=e.target;
  if(f.id!=='photo'||!f.files||!f.files[0])return;
  if(f.files[0].size>2*1024*1024){alert('Photo is bigger than 2MB. Please choose a smaller one.');f.value='';return;}
  var r=new FileReader();
  r.onload=function(){var p=document.getElementById('preview');p.src=r.result;p.hidden=false;};
  r.readAsDataURL(f.files[0]);
});