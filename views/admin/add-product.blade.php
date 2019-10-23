@extends('layouts.mainlayout')


@section('content')
<form action="/WEBASSIGNMENT2/cart" method="POST">
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="action" value="add"  />
     <input name="id" value="4"  />
      <input name="quantity" value="1"  />
    <input type="submit" value="Send File" />
</form>
@endsection

@section('javascript')
    <script>
        function getCheckedCheckbox(){
            $arr = {};
            $.each($("input:checked"), function(){
                $arr[$(this).attr('id')] = $(this).val();
            });
            return $arr;
        }
        
        $('#sub').click(function(e){
        e.preventDefault();
        $arr = getCheckedCheckbox();
        var formData = new FormData($(this).parents('form')[0]);
        formData.append('categorys', JSON.stringify($arr));
        $.ajax({
            url: 'post/add-product',
            type: 'POST',
            // xhr: function() {
            //     var myXhr = $.ajaxSettings.xhr();
            //     return myXhr;
            // },
            success: function (data) {
                alert("Data Uploaded: "+data);
            },
            error: function (err) {
                alert('err ' + err.responseText);
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
        });
    </script>
@endsection