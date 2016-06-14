//
// Hold character data
//
class AppCharacterViewClass
{
    constructor()
    {
        this.oldData = {};
        this.newData = {};
        this.lodestoneId = null;
    }

    //
    // Set characters old and new data for reference in modules
    //
    setData(oldData, newData)
    {
        this.oldData = oldData;
        this.newData = newData;
        this.lodestoneId = newData.id;
    }
}

// Export it
module.exports = new AppCharacterViewClass;
