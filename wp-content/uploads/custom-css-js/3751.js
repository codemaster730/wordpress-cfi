<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
/* Default comment here */ 

jQuery(document).ready(function( $ ){
    /* Right click , ctrl+u and ctrl+shift+i disabled */
    $('body').on('contextmenu', function (e) {
      e.preventDefault();
      e.stopPropagation();
      return false;
    });

    $(document).on('keydown', function (e) {
      if (
        (e.ctrlKey && (e.keyCode == 85)) ||
        (e.ctrlKey && (e.shiftKey && e.keyCode == 73)) ||
        (e.ctrlKey && (e.shiftKey && e.keyCode == 75)) ||
        (e.metaKey && (e.shiftKey && e.keyCode == 91))
      ) {
        return false;
      } else {
        return true;
      }
    });
});</script>
<!-- end Simple Custom CSS and JS -->
