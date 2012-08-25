;;; opac3.el --- Minor PHP mode for programming AFI-OPAC2

;; Copyright (C) 2012 Free Software Foundation, Inc.
;;
;; Author: Laurent Laffont <llaffont@afi-sa.fr>
;; Maintainer: Laurent Laffont <llaffont@afi-sa.fr>
;; Created: 6 Aug 2012
;; Version: 0.01
;; Keywords: languages, tools

;; This program is free software; you can redistribute it and/or modify
;; it under the terms of the GNU General Public License as published by
;; the Free Software Foundation; either version 2, or (at your option)
;; any later version.
;;
;; This program is distributed in the hope that it will be useful,
;; but WITHOUT ANY WARRANTY; without even the implied warranty of
;; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
;; GNU General Public License for more details.
;;
;; You should have received a copy of the GNU General Public License
;; along with this program; if not, write to the Free Software
;; Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

;; Depends on: Geben, Php-mode, phpunit-mode
;;
;; Example of .emacs setup :
;;
;; (require 'php-mode)
;; (add-to-list 'auto-mode-alist  '("\\.php[34]?\\'\\|\\.phtml\\'" . php-mode))
;; (defun my-php-mode() 
;; 	(autoload 'opac3-mode "opac3" "OPAC3 mode")
;; 	(opac3-mode)
;; )
;; (add-hook 'php-mode-hook 'my-php-mode) 

(defvar opac3-phpunit-config "~/dev/afi/afi-opac3/tests/phpunit.xml" "phpunit.xml path")
(setq opac3-phpunit-command "phpunit")


(setq opac3-mode-hook '(opac3-php-mode))
(defun opac3-php-mode()
	(require 'geben)
	(require 'phpunit)
	(require 'auto-complete)
	(auto-complete-mode t)
;;	(setq ac-sources '(ac-source-etags ac-source-words-in-same-mode-buffers))
	(setq ac-sources '(ac-source-etags))
	(imenu-add-menubar-index)

  (setq 
	 tab-width 2
   indent-tabs-mode t
	 flymake-mode t
	 compilation-error-regexp-alist  '(	("^\\(/.*\\):\\([0-9]+\\)$" 1 2)
																			("^.* \\(/.*\\):\\([0-9]+\\)" 1 2)
																			("PHP\s+[0-9]+\. [^/]* \\([^:]+\\):\\([0-9]+\\)" 1 2)
																			("in \\(/.*\\) on line \\([0-9]+\\)" 1 2) ) 
	 geben-pause-at-entry-line nil)	
	)


(defun opac3-stop-geben (buffer msg)
	(geben-end 'geben-dbgp-default-port)
	(jump-to-register 'g)
	(setq compilation-finish-functions '())
	;; (beginning-of-line)
	;; (kill-line)
	;; (kill-line)
	(save-buffer)
	)


(defun opac3-compile-phpunit (&optional params &optional debug &optional testdox)
	(save-buffer)
	(let 
			((command-filter (if params (concat " --filter " params) " "))
			 (debug-mode (if debug "XDEBUG_CONFIG=1 " ""))
			 (testdox-option (if testdox " --testdox " "")))

		(if debug (progn (geben 1) 
										 (window-configuration-to-register 'g)
										 (add-to-list 'compilation-finish-functions 'opac3-stop-geben)
										 ;; (beginning-of-line)
										 ;; (insert "xdebug_break();")
										 ;; (indent-according-to-mode)
										 ;; (align-newline-and-indent)
										 ;; (previous-line)
										 ;; (beginning-of-line)
										 (save-buffer)
										 )
			)

		(setq opac3-phpunit-command
					(concat	debug-mode "phpunit -c " opac3-phpunit-config command-filter testdox-option))

		(compile opac3-phpunit-command)
		)
	)


(defun opac3-run-phpunit(debug-mode)
	"Run all phpunit tests"
	(interactive "P" )
	(opac3-compile-phpunit nil debug-mode)
	)


(defun opac3-run-phpunit-filtered-file(debug-mode)
	"Run phpunit on this file / class"
	(interactive "P")
	(opac3-compile-phpunit 
	 (car (split-string (file-name-sans-extension (opac3-cur-file)) "Test"))
	 debug-mode)
	)


(defun opac3-run-phpunit-filtered-class(debug-mode)
	"Run phpunit on this class"
	(interactive "P")
	(opac3-compile-phpunit (phpunit-class-ap) debug-mode)
	)


(defun opac3-run-phpunit-filtered-function(debug-mode)
	"Run phpunit on this function"
	(interactive "P")
	(opac3-compile-phpunit (cdr (phpunit-function-ap)) debug-mode)
	)


(defun opac3-run-last-phpunit-command()
	"Run last phpunit command"
	(interactive)
	(compile opac3-phpunit-command)
)


(defun opac3-run-phpunit-filtered-custom(custom-filter debug testdox)
	"Prompt for a filter and run phpunit with it"
	(interactive (list (read-string "Enter PHPUnit fiter: ")
										 (y-or-n-p "Debug ?: ")
										 (y-or-n-p "Testdox format ?: ")))
	(opac3-compile-phpunit custom-filter debug testdox)
	)


(defun opac3-cur-file ()
  "Return the filename (without directory) of the current buffer"
  (file-name-nondirectory (buffer-file-name (current-buffer)))
  )


(defun opac3-debug-phpunit-function()
	"Run phpunit on this function with debugger activated"
	(interactive)
	(opac3-run-phpunit-filtered-function t)
	)


(defun opac3-strftime(start end) 
	"Interprets region as a timestamp and converts into human date"
	(interactive "r")
	(let ((selected-text (buffer-substring start end)))
		(geben-eval-expression (concat "strftime('%Y-%m-%d %H:%M:%S', " selected-text " )")))
)


(defun opac3-eval-region(start end) 
	"Eval current region in geben"
	(interactive "r")
	(let ((selected-text (buffer-substring start end)))
		(geben-eval-expression selected-text))
)


(define-minor-mode opac3-mode
  "Toggle AFI-OPAC  mode."
  :lighter " opac3"
  :keymap
	'(("\C-crf" . opac3-run-phpunit-filtered-function)
		("\C-crc" . opac3-run-phpunit-filtered-class)
		("\C-cra" . opac3-run-phpunit-filtered-file)
		("\C-crm" . opac3-run-phpunit-filtered-custom)
		("\C-crl" . opac3-run-last-phpunit-command)
		("\C-crp" . opac3-run-phpunit)
		("\C-crd" . opac3-debug-phpunit-function)
		("\C-ce" . opac3-eval-region)
		("\C-cf" . opac3-strftime))
	:after-hook 'opac3-mode-hook)

(provide 'opac3)
