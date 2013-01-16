while (true) do
  modif=$(find -d . -mtime -5s -print -type f | wc -l)  
  if test $modif -gt 0 
  then
    clear
    phpunit test/lib/*.php
  fi
  sleep 5 
done

