<?php


defined('_JEXEC') or die();

	return
	'{
		"_mode":{
			"type": "select",
			"label": "PLG_ZLFRAMEWORK_MODE",
			"help": "PLG_ZLFRAMEWORK_FLS_MODE_DESC",
			"specific": {
				"options": {
					"PLG_ZLFRAMEWORK_FILES":"files",
					"PLG_ZLFRAMEWORK_FOLDERS":"folders",
					"PLG_ZLFRAMEWORK_BOTH":"both"
				}
			},
			"default": "files"
		},
		"_default_source":{
			"type": "text",
			"label": "PLG_ZLFRAMEWORK_FLS_DEFAULT_SOURCE",
			"help": "PLG_ZLFRAMEWORK_FLS_DEFAULT_SOURCE_DESC",
			"check_old_value":{
				"id":"_default_file"
			}
		},
		"_extensions":{
			"type": "text",
			"label": "PLG_ZLFRAMEWORK_FLS_EXTENSIONS",
			"help": "PLG_ZLFRAMEWORK_FLS_EXTENSIONS_DESC",
			"default": "png|jpg|gif|bmp|doc|mp3|mov|avi|mpg|zip|rar|gz|pdf"
		},
		"_source_dir":{
			"type": "text",
			"label": "PLG_ZLFRAMEWORK_FLS_DIRECTORY",
			"help": "PLG_ZLFRAMEWORK_FLS_DIRECTORY_DESC"
		},
		"_max_upload_size":{
			"type": "text",
			"label": "PLG_ZLFRAMEWORK_FLS_MAX_UPLOAD_SIZE",
			"help": "PLG_ZLFRAMEWORK_FLS_MAX_UPLOAD_SIZE_DESC||{PHP-MAX_UPLOAD}"
		}
		'.($arguments['element']->config->find('files._max_upload_size') > substr($this->app->zlfw->filesystem->getUploadValue(), 0, -3) ?',"_info":{
			"type":"info",
			"specific":{
				"text":"PLG_ZLFRAMEWORK_FLS_MAX_UPLOAD_SIZE_INFO||{PHP-MAX_UPLOAD}"
			}
		}': '').'
		'/* Image Resizing client side */.'
		'.(isset($params['resize']) ? ',

		"_resize":{
			"type": "checkbox",
			"label": "PLG_ZLFRAMEWORK_FLS_IMG_RESIZE",
			"help": "PLG_ZLFRAMEWORK_FLS_IMG_RESIZE_DESC",
			"default": "0",
			"specific":{
				"label":"PLG_ZLFRAMEWORK_ENABLE"
			},
			"dependents": "resize_content > 1"
		},
		"resize_content":{
			"type": "wrapper",
			"control":"resize",
			"fields": {
				"avoid_resize_small":{
					"type": "radio",
					"label": "PLG_ZLFRAMEWORK_FLS_IMG_AVOID_SMALL_TO_LARGE",
					"help": "PLG_ZLFRAMEWORK_FLS_IMG_AVOID_SMALL_TO_LARGE_DESC",
					"default": "0"
				},
				"width":{
					"type": "text",
					"label": "PLG_ZLFRAMEWORK_WIDTH",
					"help": "PLG_ZLFRAMEWORK_FLS_IMG_RESIZE_WIDTH_DESC"
				},
				"height":{
					"type": "text",
					"label": "PLG_ZLFRAMEWORK_HEIGHT",
					"help": "PLG_ZLFRAMEWORK_FLS_IMG_RESIZE_HEIGHT_DESC"
				},
				"crop":{
					"type": "radio",
					"label": "Crop",
					"help": "PLG_ZLFRAMEWORK_FLS_IMG_RESIZE_CROP_DESC",
					"default": "0"
				}
			}
		}' : '').'

		'/* Amazon Integration */.'
		'.(isset($params['s3']) ? ',

		"_s3":{
			"type": "checkbox",
			"label": "PLG_ZLFRAMEWORK_FLS_S3_INTEGRATION",
			"default": "0",
			"specific":{
				"label":"PLG_ZLFRAMEWORK_ENABLE"
			},
			"dependents": "s3_content > 1"
		},
		"s3_content":{
			"type": "wrapper",
			"fields": {
				"_s3bucket":{
					"type": "text",
					"label": "PLG_ZLFRAMEWORK_FLS_S3_BUCKET",
					"help": "PLG_ZLFRAMEWORK_FLS_S3_BUCKET_DESC"
				},
				"_awsaccesskey":{
					"type": "password",
					"label": "PLG_ZLFRAMEWORK_FLS_AWS_ACCESKEY",
					"help": "PLG_ZLFRAMEWORK_FLS_AWS_ACCES_DESC"
				},
				"_awssecretkey":{
					"type": "password",
					"label": "PLG_ZLFRAMEWORK_FLS_AWS_SECRETKEY",
					"help": "PLG_ZLFRAMEWORK_FLS_AWS_ACCES_DESC"
				}
			}
		}' : '').'
	}';

?>