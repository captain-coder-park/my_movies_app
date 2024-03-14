@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
    }

    .container {
        margin-top: 50px;
    }

    .card {
        border: 1px solid #ddd;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
    }

    .input-group {
        width: 100%;
    }

    .input-group > * {
        width: 30%;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
}

.movie-details {
    display: flex;
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.poster img {
    width: 200px;
    height: auto;
    object-fit: cover;
}

.info {
    padding: 20px;
    font-size: 70%;
    line-height: 1;
}

.title {
    font-size: 24px;
    margin-top: 0;
}

.year, .rating, .genre, .director, .actors {
    margin-top: 0;
    margin-bottom: 10px;
}

.loader {
  border: 4px solid #f3f3f3; /* Light grey */
  border-top: 4px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  margin: 20px auto; /* Center loader */
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    <div class="pull-left">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('Welome to My Movies App!') }}
                    </div>
                    <div class="pull-right">
                        <button onclick="ModActions('myHistoryModal','show')" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myHistoryModal">
                            Search History <i class="fa fa-history"></i>
                        </button>
                    </div>
                </div>
                <!-- </div>
                <div class="card"> -->
                <div class="card-header">Search Movies</div>
                <div class="card-body">
                    <form id="searchForm" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Movie Name" name="t" required>
                            <!-- <input type="text" class="form-control" placeholder="Year" name="y"> -->
                            <select class="form-control" name="y">
                                <option value="">Year (Select One)</option>
                                <?php for($i=date('Y');$i>=1600;$i--){?>
                                    <option value="<?=$i;?>"><?=$i;?></option>
                                <?php }?>
                            </select>
                            <select class="form-select" name="plot">
                                <option value="all">Plot (Select One)</option>
                                <option value="short">Short</option>
                                <option value="full">Full</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container movie_result" style="display:none;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Search Results</div>
                <div class="card-body">
                    <div class="movie-details">
                        <div class="poster">
                            <img class="m_poster" src="" alt="Movie Poster">
                        </div>
                        <div class="info" style="">
                            <h1 class="m_title"></h1>
                            <p>Year: <span class="m_year"></span></p>
                            <p class="m_plot"></p>
                            <p>IMDB Rating: <span class="m_rating"></span></p>
                            <p>Genre: <span class="m_genre"></span></p>
                            <p>Director: <span class="m_director"></span></p>
                            <p>Actors: <span class="m_actors"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container no_movie_result" style="display:none;">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Search Results</div>
                <div class="card-body">
                    <div class="movie-details">
                        <div class="info" style="">
                            <h1 class="nm_title"></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="myHistoryModal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title pull-left">Your Search History</h4>
          <!-- <button type="button" class="close pull-right" data-dismiss="modal" onclick="ModActions('myHistoryModal','hide')">&times;</button> -->
        </div>
        <div class="modal-body">
          @if($search_histories != NULL)
                @foreach($search_histories as $sk => $sv)
                    <div class="row" style="padding:1%;">
                        <div class="col-sm-4">{{$sv->search_value}}</div>
                        <div class="col-sm-4">{{date('d-M-Y H:i A',strtotime($sv->created_at))}}</div>
                        <div class="col-sm-4">
                            <a href="{{ route('history.delete', ['id' => base64_encode($sv->id)]) }}"><i class="fa fa-trash"></i></a>
                        </div>
                    </div><hr>
                @endforeach
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" onclick="ModActions('myHistoryModal','hide')">Close</button>
        </div>
      </div>
      
    </div>
</div>

<div class="loader" style="display:none"></div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#searchForm').submit(function(e) {
            e.preventDefault(); // Prevent form submission
            $('.loader').css('display','block');
            // Get form data
            var formData = $(this).serialize();

            // AJAX request
            $.ajax({
                url: '{{ route("movies.search") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: formData,
                success: function(response) {
                    // Handle successful response
                    console.log('response',response.Title);
                    $('.movie_result').css('display','none');
                    $('.no_movie_result').css('display','none');
                    $('.nm_title').html(response.Error);
                    if(response.Title == undefined){
                        $('.no_movie_result').css('display','block');
                    }else{
                        $('.m_title').html(response.Title);
                        $('.m_poster').prop('src',response.Poster);
                        $('.m_year').html(response.Year);
                        $('.m_plot').html(response.Plot);
                        $('.m_rating').html(response.imdbRating);
                        $('.m_genre').html(response.Genre);
                        $('.m_director').html(response.Director);
                        $('.m_actors').html(response.Actors);
                        $('.movie_result').css('display','block');
                        $('.loader').css('display','none');
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    alert(error);
                    $('.loader').css('display','none');
                }
            });
        });
    });

    function ModActions(ModId,Action){
        $('#'+ModId).modal(Action);
    }
</script>
@endsection
