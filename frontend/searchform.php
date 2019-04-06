  <script>
  $( function() {
    var dateFormat = "mm/dd/yy",
      from = $( "#from" )
        .datepicker({
          defaultDate: "+0d",
          changeMonth: true,
          numberOfMonths: 1
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#to" ).datepicker({
        defaultDate: "+0w",
        changeMonth: true,
        numberOfMonths: 1
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });

    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }

      return date;
    }
  } );
  </script>

<script>
  $( function() {
    $( "#search" ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "autocomplete.php",
          dataType: "json",
          data: {
            term: request.term,
                category: "<?php echo $category; ?>"
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
                var label = ui.item.label;
                var split = label.split(" | ");
                $("#search").val(split[0]);
                $("#category").val(split[1]);
                document.getElementById('mainform').submit();
        }
    });
  } );
</script>

<?php 

if ($searchType == 'main') {
	
	$submitTo="index.php";
}
else {
	$submitTo="index.php?dove=search";
}
?>
<form action="<?php echo $submitTo; ?>" method="post" id="mainform">
  <div class="form-inline">
  <div class="form-group mb-2">
    <input type="text" class="form-control border border-primary" id="search" placeholder="Search" name="search" value="<?php if ($search != '*') { echo $search; } ?>">
  </div>
  <div class="form-group col-xs-2 mb-2">
        <input type="text" class="form-control border" id="from" name="from" placeholder="Date From" value="<?php if (!empty($from)) { echo $from; } ?>">
   </div>
  <div class="col-xs-2 mb-2">
        <input type="text" class="form-control border" id="to" name="to" placeholder="Date To" value="<?php if (!empty($to)) { echo $to; } ?>">
   </div>
  <div class="form-group mb-2 mx-sm-3">
    <select class="form-control mx-sm-3a" id="category" name="category">
        <?php
                if ($category == "All") {
                        echo "<option onclick=\"document.getElementById('mainform').submit();\" value=\"All\" selected>All Categories</option>";
                }
                else
                {
                        echo "<option onclick=\"document.getElementById('mainform').submit();\" value=\"All\">All Categories</option>";
                }
        foreach(json_decode($categories) as $cat) {
                if ($cat == $category) {
                        echo "<option onclick=\"document.getElementById('mainform').submit();\" value=\"" . $cat . "\" selected>" . $cat . "</option>";
                }
                else
                {

                        echo "<option onclick=\"document.getElementById('mainform').submit();\" value=\"" . $cat . "\">" . $cat . "</option>";
                }
        }
        ?>
    </select>
  </div>

  <div class="form-group mb-2 mx-sm-3">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>

   </div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" onclick="document.getElementById('mainform').submit();" id="socialFeed" name="socialFeed" value="socialfeed" <?php if ($socialFeed) echo 'checked' ; ?>>
  <label class="form-check-label" for="socialFeed">Social Feed</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" onclick="document.getElementById('mainform').submit();" name="webFeed" id="webFeed" value="webfeed" <?php if ($webFeed) echo 'checked' ; ?>>
  <label class="form-check-label" for="webFeed">Web Crawl Feed</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" onclick="document.getElementById('mainform').submit();" id="importFeed" name="importFeed" value="importfeed" <?php if ($importFeed) echo 'checked' ; ?>>
  <label class="form-check-label" for="importFeed">Imports</label>
</div>
  </div>
</form>
<br>

