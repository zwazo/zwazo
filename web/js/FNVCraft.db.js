/* FNVCraft Database
 * 
 * Datas from http://fallout.wikia.com/wiki/Fallout:_New_Vegas_crafting
 */

if (!FNVCraft) { 
	/* avoid load priority error */
	var FNVCraft = {}; // namespace
}

FNVCraft.db = {};
FNVCraft.db.name = "FNVCraftDB";
FNVCraft.db.version = 1;
FNVCraft.db.storage = 'temporary';
FNVCraft.db.ingredients = [
	{} // 0
	,{lbl:'Abraxo Cleaner'}            // 1
	,{lbl:'Ant Egg'}					
	,{lbl:'Banana yucca fruit'}
	,{lbl:'Bark scorpion poison gland'}
	,{lbl:'Barrel cactus fruit'}       // 5
	,{lbl:'Beer'}   
	,{lbl:'Bighorner meat'}
	,{lbl:'BlamCo Mac & Cheese'}
	,{lbl:'Blank book'}
	,{lbl:'Bloatfly meat'}             // 10
	,{lbl:'Blood sausage',dlc:'OWB'}
	,{lbl:'Bottle cap'}
	,{lbl:'Box of detergent'}
	,{lbl:'Brahmin meat'}
	,{lbl:'Brahmin steak',recipe:4}    // 15
	,{lbl:'Broc flower'}         
	,{lbl:'Buffalo gourd seed'}
	,{lbl:'Butter knife'}
	,{lbl:'Cave fungus'}
	,{lbl:'Cazador poison gland'}      // 20
	,{lbl:'Cherry bomb'}
	,{lbl:'Coffee mug'}
	,{lbl:'Coffee pot'}
	,{lbl:'Conductor'}
	,{lbl:'Cosmic knife',dlc:'DM'}     // 25
	,{lbl:'Cosmic knife clean',dlc:'DM'}
	,{lbl:'Coyote hide'}
	,{lbl:'Coyote meat'}
	,{lbl:'Coyote tobacco chew'}
	,{lbl:'Cram'}                      // 30
	,{lbl:'Crunchy mutfruit'}
	,{lbl:'Deathclaw egg'}
	,{lbl:'Dirty water'}
	,{lbl:'Dog hide'}
	,{lbl:'Dog meat'}                  // 35
	,{lbl:'NCR dogtags'}
	,{lbl:'Duct tape'}
	,{lbl:'Dynamite'}
	,{lbl:'Egg timer'}
	,{lbl:'Empty soda bottle'}         // 40
	,{lbl:'Empty Sunset Sarsaparilla bottle'}
	,{lbl:'Empty syringe'}
	,{lbl:'Empty whiskey bottle'}
	,{lbl:'Fire ant meat'}
	,{lbl:'Fire gecko hide'}           // 45
	,{lbl:'Fission battery'}
	,{lbl:'Flour'}
	,{lbl:'Forceps'}
	,{lbl:'Fork'}
	,{lbl:'Fresh apple'}               // 50
	,{lbl:'Fresh pear'}
	,{lbl:'Fresh potato'}
	,{lbl:'Gecko hide'}
	,{lbl:'Gecko meat'}
	,{lbl:'Glass pitcher'}             // 55
	,{lbl:'Golden gecko hide'}
	,{lbl:'Green gecko hide',dlc:'HH'}
	,{lbl:'Gum drops'}
	,{lbl:'Healing powder'}
	,{lbl:'Honey mesquite pod'}        // 60
	,{lbl:'Hot plate'}
	,{lbl:'InstaMash'}
	,{lbl:'Jalapeño pepper'}
	,{lbl:'Jar of Cloud residue',dlc:'DM'}
	,{lbl:'Jet'}                       // 65
	,{lbl:'Junk food'}
	,{lbl:'Knife'}
	,{lbl:'Knife spear'}
	,{lbl:'Lakelurk meat'}
	,{lbl:'Large whiskey bottle'}      // 70
	,{lbl:'Leather armor'}
	,{lbl:'Leather armor, reinforced'}
	,{lbl:'Leather belt'}
	,{lbl:'Lunchbox'}
	,{lbl:'Maize'}                     // 75
	,{lbl:'Mantis foreleg'}
	,{lbl:'Medical brace'}
	,{lbl:'Mentats'}
	,{lbl:'Metal armor'}
	,{lbl:'Metal armor, reinforced'}   // 80
	,{lbl:'Metal cooking pan'}
	,{lbl:'Metal cooking pot'}
	,{lbl:'Metal spoon'}
	,{lbl:'Mole rat meat'}
	,{lbl:'Mutant cave fungus',dlc:'OWB'}  // 85
	,{lbl:'Mutfruit'}
	,{lbl:'Nevada agave fruit'}
	,{lbl:'Nightstalker blood'}
	,{lbl:'Nightstalker egg'}
	,{lbl:'Nuka-Cola'}                 // 90
	,{lbl:'Nuka-Cola Quartz'}
	,{lbl:'Nuka-Cola Victory'}
	,{lbl:'Pilot light'}
	,{lbl:'Pinto bean pod'}
	,{lbl:'Pinyon nuts'}               // 95
	,{lbl:"Pork n' Beans"}
	,{lbl:'Pot'}
	,{lbl:'Prickly pear fruit'}
	,{lbl:'Psycho'}
	,{lbl:'Purified water'}            // 100
	,{lbl:'Radscorpion poison gland'}
	,{lbl:'Sacred datura root',dlc:'HH'}
	,{lbl:'Salient Green',dlc:'OWB'}
	,{lbl:'Scalpel'}
	,{lbl:'Scrap electronics'}         // 105
	,{lbl:'Scrap metal'}
	,{lbl:'Sensor module'}
	,{lbl:'Spore plant pods',dlc:'HH'}
	,{lbl:'Stimpak'}
	,{lbl:'Sugar Bombs'}               // 110
	,{lbl:'Super stimpak'}
	,{lbl:'Surgical tubing'}
	,{lbl:'Tanned fire gecko hide'}
	,{lbl:'Tanned golden gecko hide'}
	,{lbl:'Tanned green gecko hide'}   // 115
	,{lbl:'Thin red paste',dlc:'OWB'}
	,{lbl:'Throwing knife spear',dlc:'DM'}
	,{lbl:'Tin can'}
	,{lbl:'Tin plate'}
	,{lbl:'Turpentine'}                // 120
	,{lbl:'Vodka'}
	,{lbl:'Whiskey'}
	,{lbl:'White horsenettle'}
	,{lbl:'Wine'}
	,{lbl:'Wonderglue'}                // 125
	,{lbl:'Wrench'}
	,{lbl:'Xander root'}
	,{lbl:'Yeast'}	
];

FNVCraft.db.tool = [
	[] // 0
	,['Campfire','Electric Hot Plate','Oven']
	,['Reloading Bench']
	,['Workbench']
];

FNVCraft.db.recipes = [
// s = survival
// r = recipe
	{} // 0
// Food
	,{lbl:'Bighorner steak',kind:'food',s:50,r:1,tool:1,need:['7:1']}
	,{lbl:'Black blood sausage ',kind:'food',s:50,r:0,tool:1,need:['11:1','85:1','124:1','127:1'],dlc:'OWB'}
	,{lbl:'Bloatfly slider',kind:'food',s:20,r:0,tool:1,need:['10:1','98:2']}
	,{lbl:'Brahmin steak',kind:'food',s:35,r:0,tool:1,need:['14:1']}
	,{lbl:'Brahmin Wellington',kind:'food',s:80,r:1,tool:1,need:['2:2','8:1','14:1']}
	,{lbl:'Caravan lunch',kind:'food',s:30,r:0,tool:1,need:['30:1','62:1','74:1','96:1']}
	,{lbl:"Cook-Cook's Fiend stew",kind:'food',s:75,r:1,tool:1,need:['6:2','14:1','52:1','63:1']}
	//,{lbl:'',kind:'food',skill:[],tool:1,need:[]}
];