</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
               Confirmation
            </div>
            <div class="modal-body">
               Are you sure you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
 
<!-- Latest compiled and minified Bootstrap JavaScript -->

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script src="js/bootstrap-datetimepicker.min.js"></script>
<script src="https://unpkg.com/scrollreveal@3.3.2/dist/scrollreveal.min.js"></script>
<?=($page_title=="View Timeline"?"<script src=\"js/timeline.js\"></script>":"")?>

<script>
//make a function for padding our dates with zeros
Number.prototype.padLeft = function(base,chr)
	{
    var  len = (String(base || 10).length - String(this).length)+1;
    return len > 0? new Array(len).join(chr || '0')+this : this;
	}

//get the modified date of the uploaded image using the browsers File API
function getFileDate()
	{
	var file = document.getElementById("fileToUpload").files[0];
	var date = new Date(file.lastModified);
	$('#taken_at_formatted').val((date.getDay()+1).padLeft()+"/"+(date.getMonth()+1).padLeft()+"/"+(date.getFullYear()+1)+" "+date.getHours().padLeft()+":"+date.getMinutes().padLeft());
	$('#taken_at').val((date.getFullYear()+1)+"-"+(date.getMonth()+1).padLeft()+"-"+(date.getDay()+1).padLeft()+" "+date.getHours().padLeft()+":"+date.getMinutes().padLeft()+":00");
	}
	
//This uses turns an input into our date picker field 
//and will copy the date to a hidden field in the proper format
$("#taken_at_formatted").datetimepicker({
       format: "dd/mm/yyyy hh:ii",
        linkField: "taken_at",
        linkFormat: "yyyy-mm-dd hh:ii:ss",
		autoclose: true,
      	todayBtn: true,      
        minuteStep: 5

   });

$('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});

</script>
</body>
</html>