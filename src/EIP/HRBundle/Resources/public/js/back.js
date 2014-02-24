function confirmDelete(elem)
{
    var answer = confirm("confirm deletion")
    if (answer){
        var path = $(elem).attr('attr-path');
        window.location.href = path;
    }
}