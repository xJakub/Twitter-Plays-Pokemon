nextcommandsfile = io.open('nextcommands.lua', 'r')
nextcommandsfile:seek("end")

efected = 0
current = nextcommandsfile:seek()
lasttime = os.time()-10
lastsavetime = os.time()-60
saveslot = 1

 ktab = {}
 ktab["A"]=0
 ktab["B"]=0
 ktab["up"]=0
 ktab["down"]=0
 ktab["left"]=0
 ktab["right"]=0
 ktab["start"]=0
 ktab["select"]=0

while true do
  
  newcurrent = nextcommandsfile:seek()


  
  currenttime = os.time()
  
  if currenttime >= lasttime + 10 then
	scdata = gui.gdscreenshot()
	
	scfile = io.open('screenshots/'..currenttime..'.png','wb')
	scfile:write(scdata)
	scfile:close()
	
	lasttime = currenttime
  end
  
  if currenttime >= lastsavetime + 60 then
	svdata = savestate.create()
	print(debug.getmetatable(svdata))
	saveslot = saveslot + 1
	if (saveslot == 13) then
		saveslot = 1
	end
	
	savestate.save(svdata)
	
	lastsavetime = currenttime
  end
	  
  if newcurrent == current then
	  
	  currentend = nextcommandsfile:seek("end")
	  nextcommandsfile:seek("set", current)
	  
	  
	  while current < currentend do
	    efected = efected + 1
		nline = nextcommandsfile:read()
		newcurrent = nextcommandsfile:seek()
		
		print(efected..' '..current..'-'..newcurrent..'<'..currentend..': '..nline)
		
		if newcurrent == current + string.len(nline) +2 then
			loadstring(nline)()
			current = newcurrent
		else
			nextcommandsfile:seek("set", current)
			break
		end
		
		
	  end
	  
	--  if current > currentend then
	--	current = currentend
	--  end

	end
	
	k2tab = {}
	
 if (ktab["A"]>0) then
	k2tab["A"]=1
	ktab["A"]=ktab["A"]-1
end

 if (ktab["B"]>0) then
	k2tab["B"]=1
	ktab["B"]=ktab["B"]-1
end

 if (ktab["up"]>0) then
	k2tab["up"]=1
	ktab["up"]=ktab["up"]-1
end

 if (ktab["down"]>0) then
	k2tab["down"]=1
	ktab["down"]=ktab["down"]-1
end

 if (ktab["left"]>0) then
	k2tab["left"]=1
	ktab["left"]=ktab["left"]-1
end

 if (ktab["right"]>0) then
	k2tab["right"]=1
	ktab["right"]=ktab["right"]-1
end

 if (ktab["start"]>0) then
	k2tab["start"]=1
	ktab["start"]=ktab["start"]-1
end
 if (ktab["select"]>0) then
	k2tab["select"]=1
	ktab["select"]=ktab["select"]-1
end
	
  joypad.set(1,k2tab)
  emu.frameadvance()
end