-- �뼶�����ű�[����]
-- by �������� 2020-9-22 16:03:33

-- �ű�ID
x666898_g_scriptId	= 666898
--**********************************
--�������
--**********************************
function x666898_OnCharacterTimer( sceneId, objId, dataId, uTime )
	-- ֻ��ScriptGlobal�п�����GM���߹��߲Ż���Ч
	if GMDATA_ISOPEN_GMTOOLS == 1 then
		-- ͨ��API��ȡ��������д�뵽SecondsTimerData.txt
		execute("cd /home/tlbb/Server/SecondsTimer;wget -q 'http://�����������IP/index.php?privateKey=����PHP�ļ������õ���֤KEY' -O SecondsTimerData.txt")
		-- ÿ��ֻ��ȡ��һ������
		local SecondsTimerData = openfile("./SecondsTimer/SecondsTimerData.txt", "r")
		local DataStr = read(SecondsTimerData, "*l")
		closefile(SecondsTimerData)
		-- ִ������¼���ʼ
		if DataStr ~= nil then
			local _,_,id,event,param1,param2,param3,param4 = strfind(DataStr,"(.*),(.*),(.*),(.*),(.*),(.*)")
			if event == 'SendGlobalNews' then -- ������
				x666898_SendGlobalNews(sceneId, param1)
			end
			if event == 'GivePlayerItem' then -- ����Ʒ
				x666898_GivePlayerItem(sceneId, param1, param2, param3)
			end
			if event == 'SetPlayerLevel' then -- ��������ȼ�
				x666898_SetPlayerLevel(sceneId, param1, param2)
			end
			if event == 'GivePlayerYuanBao' then -- ��Ԫ��
				x666898_GivePlayerYuanBao(sceneId, param1, param2)
			end
		end
		if DataStr ~= nil then
			local _,_,id,event,param1,param2,param3,param4 = strfind(DataStr,"(.*),(.*),(.*),(.*),(.*),(.*)")
			local SecondsTimerLog = openfile("./SecondsTimer/SecondsTimerLog.txt", "a+")
			write(SecondsTimerLog, "["..date('%Y-%m-%d %H:%M:%S').."][id]:"..id..",[Event]��"..event..",[Param1]��"..param1..",[Param2]��"..param2..",[Param3]��"..param3..",[Param4]��"..param4.."\n")
			closefile(SecondsTimerLog)
		end
		-- ִ�����¼�����
	end
end

--**********************************
--����������ƻ�����ǳ�����ָ����ҵ�objId ��ʱ
--**********************************
function x666898_GetScenePlayerObjId(PlayerName)
	local sceneIdList = {
		0, -- ����
		1, -- ����
		2, -- ����
		186, -- ¥��
	}
	local sId = ''
	local objId = ''

	--�������Ƿ���ĳ������
	for _, tmpSceneId in sceneIdList do
		local RenNum = LuaFnGetCopyScene_HumanCount(tmpSceneId)
		for i=0, RenNum-1 do
			local EveryBodyID = LuaFnGetCopyScene_HumanObjId(tmpSceneId, i)	  --ȡ�õ�ǰ�������˵�objId
			if GetName(tmpSceneId, EveryBodyID) == PlayerName then
				sId = tmpSceneId
				objId = EveryBodyID
				break
			end
		end
		if sId ~= nil then
			break
		end
	end
	
	if objId == nil then
		objId = 0
	end

	return objId
end


--**********************************
--���õȼ�
--**********************************
function x666898_SetPlayerLevel(sceneId, PlayerName, level)
	local RenNum = LuaFnGetCopyScene_HumanCount( sceneId )
	for i=0, RenNum-1 do
		local EveryBodyID = LuaFnGetCopyScene_HumanObjId( sceneId, i )	  --ȡ�õ�ǰ�������˵�objId
		if GetName(sceneId, EveryBodyID) == PlayerName then
			SetLevel(sceneId, EveryBodyID, level)
			LuaFnSendSpecificImpactToUnit(sceneId, EveryBodyID, EveryBodyID, EveryBodyID, 49, 0)
			x666898_tips(sceneId, EveryBodyID, '����ԱΪ�������ȼ��ɹ���')
		end
	end
end

--**********************************
--���������Ʒ
--**********************************
function x666898_GivePlayerItem(sceneId, PlayerName, itemId, num)
	local RenNum = LuaFnGetCopyScene_HumanCount( sceneId )
	for i=0, RenNum-1 do
		local EveryBodyID = LuaFnGetCopyScene_HumanObjId( sceneId, i )	  --ȡ�õ�ǰ�������˵�objId
		if GetName(sceneId, EveryBodyID) == PlayerName then
			BeginAddItem(sceneId)
				AddItem(sceneId, itemId, num)
			EndAddItem(sceneId,EveryBodyID)
			AddItemListToHuman(sceneId,EveryBodyID)
			LuaFnSendSpecificImpactToUnit(sceneId, EveryBodyID, EveryBodyID, EveryBodyID, 49, 0)
			x666898_tips(sceneId, EveryBodyID, '����ԱΪ��������Ʒ�����ڱ����в��գ�')
		end
	end
end

--**********************************
--�������Ԫ��
--**********************************
function x666898_GivePlayerYuanBao(sceneId, PlayerName, yuanbaoNum)
	local RenNum = LuaFnGetCopyScene_HumanCount( sceneId )
	for i=0, RenNum-1 do
		local EveryBodyID = LuaFnGetCopyScene_HumanObjId( sceneId, i )	  --ȡ�õ�ǰ�������˵�objId
		if GetName(sceneId, EveryBodyID) == PlayerName then
			YuanBao(sceneId,EveryBodyID,-1,1,yuanbaoNum)
			LuaFnSendSpecificImpactToUnit(sceneId, EveryBodyID, EveryBodyID, EveryBodyID, 49, 0)
			x666898_tips(sceneId, EveryBodyID, '����ԱΪ������Ԫ����'..yuanbaoNum..' ,��ע����գ�')
		end
	end
end

--**********************************
--����ȫ�ֹ�������
--**********************************
function x666898_SendGlobalNews(sceneId, notice)
	local noticeFormat = format ("@*;SrvMsg;SCA:"..notice)
	AddGlobalCountNews(sceneId,noticeFormat)
end

--**********************************
--��ʾ����
--**********************************
function x666898_tips(sceneId, selfId, Tip)
	BeginEvent(sceneId)
		AddText(sceneId, Tip)
	EndEvent(sceneId)
	DispatchMissionTips(sceneId, selfId)
end