<?php
namespace App\Classes;
use Illuminate\Support\Facades\Storage;
/**
 * Description of FUpload
 *
 * @author achox
 */

class FUpload {
    public static function UPLOAD($FL,$path,$section){
           $name = $FL->getClientOriginalName();
           $ext = $FL->getClientOriginalExtension();
           $NEWNAME = $section."_".sha1($name).".".$ext;
        //    $PUT = Storage::disk('public_uploads')->putFileAs($path, $FL,$NEWNAME);
            $PUT = $FL->move(public_path($path), $NEWNAME);
           if($PUT){
               $OBJ = (object)["FILENAME"=>$NEWNAME,"PATH"=>$path,"NAME"=>$name];
               return $OBJ;
           }else{
               return false;
           }
    }

    public static function UPLOAD_MULTI($FL, $path){
        
    }
    
    public static function UPDATE($IDFile,$UPL){
        $DB = \App\Models\Files::find($IDFile);
        if($DB){
           $DEL = Storage::disk('local')->delete($DB->path."/".$DB->filename); 
           
           $UPD = $DB->update([
                                            "realname"=>$UPL->NAME,
                                            "filename"=>$UPL->FILENAME,
                                            "path"=>$UPL->PATH
                                ]);
        }
    }
    
    public static function HAPUS($IDFile){
        $DB = \App\Models\Files::find($IDFile);
        if($DB){
           $DEL = Storage::disk('local')->delete($DB->path."/".$DB->filename); 
           $DB->delete();
           return true;
        }else{
            return false;
        }
    }
    
    public static function STORE($FL,$PATH,$AUTHOR=0){
        $UPL = self::UPLOAD($FL, $PATH);
        if($UPL){
            $DBFILE = \App\Models\Files::create([
                          "realname"=>$UPL->NAME,
                          "filename"=>$UPL->FILENAME,
                          "path"=>$UPL->PATH,
                          "author_id"=>$AUTHOR
                      ]);
            if($DBFILE){
                return $DBFILE->id;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public static function URL($ID) {
           $FL = \App\Models\Files::find($ID);
           if($FL){
                $FOTO = Storage::disk('local')->exists($FL->path."/".$FL->filename);
                if($FOTO){
                    $path = str_replace("public/", "", $FL->path);             
                    $thumburl = asset('storage/'.$path."/".$FL->filename);
                    $url = $thumburl;
                }else{
                    $url = "";
                }
           }else{
              $url = "";
           }
        return $url;
    }
    public static function FILE_DETAIL($ID){
        $FL = \App\Models\Files::find($ID);
        return $FL;
    }
}
