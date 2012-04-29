History = function()
{
	var obj = {
		hashes: []
	};
	
	obj.hashes.push(location.hash);

	obj.compare = function(hash)
	{
		return (location.hash != obj.hashes[obj.hashes.length - 1]) ? false : true;
	}
	
	obj.set = function(hash)
	{
		obj.hashes.push(hash);
	}
	
	obj.get = function()
	{
		return obj.hashes[obj.hashes.length - 1];
	}
	
	obj.back = function()
	{
		obj.prev();
		iface.show_page(obj.parse_hash());
        return false;
	}
	
	obj.prev = function()
	{
		var len = obj.hashes.length;
        if (len > 1) {
            var h = obj.hashes[len - 2].substring(1);
            obj.hashes.splice(len - 1, 1);
            location.hash = h;
        }
	}
	
	obj.parse_hash = function()
	{
		return location.hash.substring(1);
	}
	
	obj.set_hash = function(hash)
	{
		if(typeof hash != 'undefined')
			{ location.hash = '#' + hash; }
		else
			{ return false; }
	}
			
	return obj; 
}