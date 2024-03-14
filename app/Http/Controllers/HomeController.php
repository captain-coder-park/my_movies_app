<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\search_histories;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $user = Auth::user();
        $userId = $user->id;
        $data['search_histories'] = search_histories::where('created_by',$userId)->orderBy('id', 'desc')->get();
        return view('home',$data);
    }

    public function search(Request $request)
    {
        $getOMDB_Entity = Settings::where('entity_name', 'OMDB_API')->first();
        $omdbApiKey = $getOMDB_Entity->entity_value;
        $OMDB_Url = "http://www.omdbapi.com/?apikey=".$omdbApiKey."&";
        foreach($_POST as $key => $val){
            if($key != "_token"){
                $_POST[$key] = htmlspecialchars(trim($val));
                $OMDB_Url .= $key."=".$_POST[$key]."&"; 
            }
        }

        if($_POST['t'] != NULL){
            // Insert search history
            $user = Auth::user();
            $userId = $user->id;
            search_histories::create([
                'search_value' => $_POST['t'],
                'created_by' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        //echo"<pre>";print_r($_POST);die;
        // Initialize cURL session
        $ch = curl_init();
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $OMDB_Url); // Set URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_HEADER, false); // Exclude header from response

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            // cURL error occurred
            $error = curl_error($ch);
            echo "cURL Error: " . $error;
            curl_close($ch);
            $html = '<h2>
                Server Error Occurs!, Please Try again with a different OMDB API!<br>
                You can update the key using settings.
            </h2>';
            return $error;
        } else {
            return $response;
            curl_close($ch);
        }
    }

    public function destroy($id)
    {
        $id= base64_decode($id);
        // Find the history record you want to delete
        $searchHistory = search_histories::find($id);

        // Check if the record exists
        if ($searchHistory) {
            // If the record exists, delete it
            $searchHistory->delete();

            // Redirect back with success message
            return redirect()->back()->with('success', 'Record deleted successfully.');
        } else {
            // If the record does not exist, redirect back with error message
            return redirect()->back()->with('error', 'Record not found.');
        }
    }
}
