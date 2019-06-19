$('#add-image').click(function () { // je recupere les numeros des future champs
    const index = + $('#widgets_counter').val();
    console.log(index);
    // je recupere le nom de prototype
    const templ = $('#annonce_images').data('prototype').replace(/__name__/g, index);
    $("#annonce_images").append(templ);
    $("#widgets_counter").val(index + 1);
    // appeler la function qui supprimer les images
    handleDeleteButtons();
});

// une fonction qui gere les bouton de suppressions
function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = + $('#annonce_images div.fom-group').lenght;
    $('#widgets_counter').val(count);
}
updateCounter();
handleDeleteButtons();