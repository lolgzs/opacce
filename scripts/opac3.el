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


(setq opac3-mode-hook '(opac3-php-mode))
(defun opac3-php-mode()
	(require 'geben)
	(require 'phpunit)
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

(defun opac3-compile-phpunit (&optional params &optional debug)
	(save-buffer)
	(let 
			((command-filter (if params (concat " --filter " params) " "))
			 (debug-mode (if debug "XDEBUG_CONFIG=1 " ""))
			 phpunit-command)

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


(defun opac3-run-phpunit-filtered-custom(custom-filter debug)
	"Prompt for a filter and run phpunit with it"
	(interactive (list (read-string "Enter PHPUnit fiter: ")
										 (y-or-n-p "Debug ?: ")))
	(opac3-compile-phpunit custom-filter debug)
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


(define-minor-mode opac3-mode
  "Toggle AFI-OPAC  mode."
  :lighter " opac3"
  :keymap
	'(("\C-crf" . opac3-run-phpunit-filtered-function)
		("\C-crc" . opac3-run-phpunit-filtered-file)
		("\C-crm" . opac3-run-phpunit-filtered-custom)
		("\C-cra" . opac3-run-phpunit)
		("\C-crd" . opac3-debug-phpunit-function))
	:after-hook 'opac3-mode-hook)

(provide 'opac3)
