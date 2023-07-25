(function ($) {
    "use strict";
    $(document).on('click','.user-detail',function(){
        var user_id = $(this).data('user_id');
        if(user_id)
        {
            $.ajax({
                type: "POST",
                url: BASE_URL + "admin/users/get_user_custom_field",
                data: {
                    'user_id': user_id
                },
                success: function (response) 
                {
                    response = JSON.parse(response);
                    if(response.status == 'success')
                    {
                        $('.custom-data').html(response.data);
                    }
                    else
                    {
                        $('.custom-data').html('');   
                    }
                },
                error: function (data) 
                {
                    console.log(data);
                }
            });    
        }
    });
})(jQuery); 