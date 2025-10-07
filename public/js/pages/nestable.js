//[Javascript]



$(function () {
    "use strict";   

	// Nestable
        $(document).ready(function () {
            $('#nestable').nestable({
                group: 1
            });
            $('#nestable2').nestable({
                group: 1
            });
            $('#nestable-menu').on('click', function (e) {
                var target = $(e.target)
                    , action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });
            $('#nestable-menu').nestable();
        });
	
  }); // End of use strict