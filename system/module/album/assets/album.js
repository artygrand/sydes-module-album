$(document).ready(function(){
	$('#add').click(function(){
		CKFinder.popup({basePath:'/vendor/ckfinder/', selectActionFunction:addPictures})
	})
	
	$('.delete').click(function(){
		var id = $(this).data('id');
		$(this).parents('.pic').remove();
		$.ajax({
			url: '?route=album/delpic',
			data: {id: id}
		})
	})
	
	$('#album').sortable({
		placeholder: 'ui-state-highlight pic',
		forcePlaceholderSize: true,
		cancel: '.delete, .empty',
		stop: function(e, ui){
				var pics = [];
				$('.delete').each(function(){
					pics.push($(this).data('id'));
				})
				$.ajax({
					url:'?route=album/sort',
					data:{pics:pics}
				})
			}
	}).disableSelection();
})

function addPictures(fileUrl, data, allFiles){
	var files = [];
	for (var key in allFiles){
		files.push(allFiles[key]['url']);
	}
	files = array_unique(files);

	$.ajax({
		url: '?route=album/addpic',
		data: {
			files: files,
			id: $('#album_id').val()
		},
		complete: function(){
			location.reload()
		}
	})
}

$(function(){
	function split(val){
		return val.split(/,\s*/);
	}
	function extractLast(term){
		return split(term).pop();
	}

	$(document).on('keydown', '[name="tags"]', function(event){
		if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete('instance').menu.active){
			event.preventDefault();
		}
	})
	$(document).on('focus', '[name="tags"]:not(.ui-autocomplete-input)', function (e){
		$(this).autocomplete({
			source: function(request, response){
				$.ajax({
					dataType: "json",
					url: '?route=album/gettag',
					data: {term: extractLast(request.term)},
					success: response
				});
			},
			search: function(){
				var term = extractLast(this.value);
				if (term.length < 2){
					return false;
				}
			},
			focus: function(){
				return false;
			},
			select: function(event, ui){
				var terms = split(this.value);
				terms.pop();
				terms.push(ui.item.value);
				terms.push('');
				terms = array_unique(terms);
				this.value = terms.join(', ');
				return false;
			}
		});
	});
});