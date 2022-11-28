<?php

namespace App\Http\Controllers;

use App\Models\UrlShortcode;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ShortLinkController extends Controller
{

	/**
	 * @OA\Post(
	 ** path="/short-url/list",
	 *   tags={"Project"},
	 *   summary="list short url from admin side",
	 *   operationId="list url details",
	 *
     *   security={{"bearer_token":{}}},
	 *   @OA\Parameter(
	 *      name="original_url",
	 *      in="query",
	 *      required=false,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="short_url",
	 *      in="query",
	 *      required=false,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="page",
	 *      in="query",
	 *      required=false,
	 *      @OA\Schema(
	 *           type="integer"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="per_page",
	 *      in="query",
	 *      required=false,
	 *      @OA\Schema(
	 *           type="integer"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
    public function index(Request $request)
    {
        try {
            $postData  = $request->all();
			$pageNumber = !empty($postData['page']) ? $postData['page'] : 1;
			$pageLimit  = !empty($postData['per_page']) ? $postData['per_page'] : 10;
			$skip       = ($pageNumber - 1) * $pageLimit;
			$query = \DB::table('url_shortcode');
			if(!empty($postData['original_url'])) {
				$query->orWhere('url', 'LIKE', '%'.$postData['original_url'].'%');
			}
			if(!empty($postData['short_url'])) {
				$query->orWhere(\DB::raw("CONCAT('".env("API_URL")."','/', url_shortcode.hash)"), 'LIKE', '%'.$postData['short_url'].'%');
			}
			$count = $query->count();
			$rows = $query->limit($pageLimit)
                ->skip($skip)
                ->orderBy('updated_at', 'DESC')
				->get([
					'url_shortcode.id', \DB::raw('url_shortcode.url AS original_url'), 'url_shortcode.is_used',  'url_shortcode.created_at', 'url_shortcode.updated_at',
					\DB::raw('(SELECT COUNT(url_visitor.id) FROM url_visitor WHERE url_visitor.url_shortcode_id= url_shortcode.id) AS visitor_count'),
					\DB::raw("CONCAT('".env("API_URL")."','/', url_shortcode.hash) AS short_url"),
				]);
            return $this->sendResponse('Short url list retrived successfully!', compact('rows', 'count'), true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to list short url!', ["general" => $e->getMessage()], 500, true);
        }
    }
	/**
	 * @OA\Post(
	 ** path="/short-url/store",
	 *   tags={"Project"},
	 *   summary="create short url from admin side",
	 *   operationId="store url details",
	 *
     *   security={{"bearer_token":{}}},
	 *   @OA\Parameter(
	 *      name="url",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
    public function store(Request $request)
    {
        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'url' => 'required|url|unique:url_shortcode,url,null,id,is_used,0',
            ], [
                'url.required'  => 'Please enter URL!',
                'url.url'       => 'Please enter valid URL!',
                'url.unique'    => 'URL already exists!',

            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to create short url!', $validator->errors(), 400, true);
            }
            \DB::beginTransaction();
            $link = UrlShortCode::create([
                'url'   => $postData['url'],
                'hash'  => base64_encode(crc32($postData['url'])),
            ]);
            \DB::commit();
			$accessUrl = env('API_URL').'/'.$link->hash;
            return $this->sendResponse('Short url created successfully!', compact('link', 'accessUrl'), true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to create short url!', ["general" => $e->getMessage()], 500, true);
        }
    }	
    /**
	 * @OA\Get(
	 ** path="/short-url/{id}/details",
	 *   tags={"Project"},
	 *   summary="Get url details admin side",
	 *   operationId="get url details",
	 *
     *   security={{"bearer_token":{}}},
	 *   @OA\Parameter(
	 *      name="id",
	 *      in="path",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="integer"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
    public function show($id)
    {
        try {
            $link = UrlShortcode::find($id);
            if (empty($link)) {
                return $this->sendError('URL not found!', ['general' =>"Url not found!"], 404, true);
            }
            $link->load('visitors');
            return $this->sendResponse('URL retrived successfully!', compact('link'), true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to show link details!', ["general" => $e->getMessage()], 500, true);
        }
    }

	/**
	 * @OA\Post(
	 ** path="/short-url/{id}/update",
	 *   tags={"Project"},
	 *   summary="create short url from admin side",
	 *   operationId="update url details",
	 *
     *   security={{"bearer_token":{}}},
	 *   @OA\Parameter(
	 *      name="id",
	 *      in="path",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="integer"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="url",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
    public function update($id,Request $request)
    {
        try {
            $postData  = $request->all();
            $validator = \Validator::make($postData, [
                'url' => 'required|url|unique:url_shortcode,url,'.$id.',id,is_used,0',
            ], [
                'url.required'  => 'Please enter URL!',
                'url.url'       => 'Please enter valid URL!',
                'url.unique'    => 'URL already exists!',

            ]);

            if ($validator->fails()) {
                return $this->sendError('Failed to update short url!', $validator->errors(), 400, true);
            }
			$link =UrlShortCode::find($id);
			if(empty($link)) {
				return $this->sendError('Url not found!', ['general' => "Url not found!"], 404, true);
			}
			if(!empty($link->is_used)) {
				return $this->sendError('Url already used!', ['general' => "Url already used!"], 400, true);
			}
            \DB::beginTransaction();
            $link->update([
                'url'   => $postData['url'],
                'hash'  => base64_encode(crc32($postData['url'])),
            ]);
            \DB::commit();
            return $this->sendResponse('Short url update successfully!', $link, true);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to update short url!', ["general" => $e->getMessage()], 500, true);
        }
    }

	/**
	 * @OA\Get(
	 ** path="/{hash}",
	 *   tags={"Project"},
	 *   summary="Get redirected by short url to original url for end user use",
	 *   operationId="process url",
	 *
	 *   @OA\Parameter(
	 *      name="hash",
	 *      in="path",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *       description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=401,
	 *       description="Unauthenticated"
	 *   ),
	 *   @OA\Response(
	 *      response=400,
	 *      description="Bad Request"
	 *   ),
	 *   @OA\Response(
	 *      response=404,
	 *      description="not found"
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Forbidden"
	 *   ),
	 *   @OA\Response(
	 *      response=500,
	 *      description="Server Error"
	 *   )
	 *)
	 **/
	public function process($hash)
    {
        try {
            $link = UrlShortcode::where('hash', $hash)->first();

            if (empty($link)) {
                return $this->sendError('Url not found!', ["general" => 'Url not found!'], 404, false);
            }

            if ($link->is_used == 1) {
                return $this->sendError('Url already used!', ["general" => 'Url already used!'], 400, false);
            }
            \DB::beginTransaction();
			$link->update(['is_used' => 1]);
			$agent = new Agent();
            $link->visitors()->create([
                'os'      => $agent->platform(),
                'ip'      => request()->ip(),
                'device'  => $agent->device(),
                'browser' => $agent->browser(),
            ]);
            \DB::commit();
            return redirect()->to($link->url);
        } catch (Exception $e) {
            \DB::rollBack();
            return $this->sendError('Failed to show link details!', ["general" => $e->getMessage()], 500, true);
        }
    }
}
