;; (require 'opac3)
;; (setq 'opac3-phpunit-config "~/path/to/phpunit.xml")


(require 'php+-mode)
(php+-mode-setup)

(require 'phpunit)
(require 'geben)


(defvar opac3-phpunit-config "~/dev/afi/afi-opac3/tests/phpunit.xml" "phpunit.xml path")


(defun opac3-php-mode()
  (setq tab-width 2)
  (setq indent-tabs-mode t)
	(setq flymake-mode t)
  (custom-set-variables
   '(geben-pause-at-entry-line nil))
	)

(autoload 'geben "geben" "PHP Debugger on Emacs" t)
(add-hook 'php+-mode-hook 'opac3-php-mode)

(defun opac3-stop-geben (buffer msg)
	(geben-end 'geben-dbgp-default-port)
	(jump-to-register 'g)
	(setq compilation-finish-functions '())
	(beginning-of-line)
	(kill-line)
	(kill-line)
	(save-buffer)
	)

(defun opac3-compile-phpunit (&optional params &optional debug)
	(save-buffer)
	(let 
			((command-filter (if params (concat " --filter " params) " "))
			 (debug-mode (if debug "XDEBUG_CONFIG=1 " ""))
			 phpunit-command)

		(if debug (progn (geben 1) 
										 (window-configuration-to-register 'g)
										 (add-to-list 'compilation-finish-functions 'stop-geben)
										 (beginning-of-line)
										 (insert "xdebug_break();")
										 (indent-according-to-mode)
										 (align-newline-and-indent)
										 (previous-line)
										 (beginning-of-line)
										 (save-buffer)
										 )
			)

		(setq phpunit-command
					(concat	debug-mode "phpunit -c " opac3-phpunit-config command-filter))

		(compile phpunit-command)
		)
	)

(defun opac3-cur-file ()
  "Return the filename (without directory) of the current buffer"
  (file-name-nondirectory (buffer-file-name (current-buffer)))
  )


(defun opac3-run-phpunit-filtered-file()
	"Run phpunit on this file / class"
	(interactive)
	(opac3-compile-phpunit (car (split-string (file-name-sans-extension (opac3-cur-file)) "Test")))
	)


(defun opac3-run-phpunit-filtered-function()
	"Run phpunit on this function"
	(interactive)
	(opac3-compile-phpunit (cdr (phpunit-function-ap)))
	)


(defun opac3-debug-phpunit-function()
	"Run phpunit on this function with debugger activated"
	(interactive)
	(opac3-compile-phpunit (cdr (phpunit-function-ap)) t)
	)


(defun opac3-run-phpunit()
	"Run all phpunit tests"
	(interactive)
	(opac3-compile-phpunit)
	)

(setq compilation-error-regexp-alist
 		  (cons 
			 '("^\\(/.*\\):\\([0-9]+\\)$" 1 2)
			 '("^.* \\(/.*\\):\\([0-9]+\\)" 1 2)
			 ))


(define-minor-mode opac3-mode
  "Toggle AFI-OPAC  mode."
  :lighter " opac3"
  :keymap
	'(("\C-crf" . opac3-run-phpunit-filtered-function)
		("\C-crc" . opac3-run-phpunit-filtered-file)
		("\C-cra" . opac3-run-phpunit)
		("\C-crd" . opac3-debug-phpunit-function)))

;;(add-to-list 'auto-mode-alist  '("\\.php[34]?\\'\\|\\.phtml\\'" . opac3-mode))

(provide 'opac3)
