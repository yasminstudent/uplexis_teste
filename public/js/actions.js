//Requisição para capturar carro(s)
$("#search_car").submit(function (event){
    event.preventDefault();
    let term = $("input[name=term]").val();

    $.ajax({
        url: "/search",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        data: {
            "term": term
        },
        beforeSend: function () {
            $("#double_req_block").removeClass('d-none')
        },
        success: function(res){
            $("#double_req_block").addClass('d-none')
            alert(res.data.message);
            if(res.data.status == 201) {
                window.location.href = "/index";
            }
        },
        error: function(res){
            $("#double_req_block").addClass('d-none')
            alert(res.responseJSON.data.message);
        }
    })
})

//Ação de deletar carro
$(".btn-delete").on("click", function (event){
    let confirm = window.confirm("Deseja excluir esse carro?");
    if(!confirm) event.preventDefault();
})
